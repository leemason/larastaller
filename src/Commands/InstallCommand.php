<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 02/11/15
 * Time: 16:59
 */

namespace LeeMason\Larastaller\Commands;


use Illuminate\Console\Command;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use LeeMason\Larastaller\Events\AfterInstallEvent;
use LeeMason\Larastaller\Events\BeforeInstallEvent;
use LeeMason\Larastaller\Installer;
use LeeMason\Larastaller\TaskRunException;

class InstallCommand extends Command
{

    protected $signature = 'installer:install {--path= : Supply a path to key > value pairs in json format when running the installer, especially useful when used in conjunction with --no-interaction}';

    protected $description = 'Installs the application';

    private $installer;

    private $filesystem;

    private $dispatcher;


    public function __construct(Installer $installer, Filesystem $filesystem, Dispatcher $dispatcher)
    {
        parent::__construct();
        $this->installer = $installer;
        $this->filesystem = $filesystem;
        $this->dispatcher = $dispatcher;
    }

    public function handle()
    {
        if($this->installer->getInstallation()->isInstalled()){
            $this->error('Your application is already installed!');
            return;
        }

        $this->dispatcher->fire(new BeforeInstallEvent($this->installer, $this->getOutput()));

        $this->info('Welcome to the installer!');

        //test requirements
        $this->info('Checking Requirements...');

        $messages = $this->installer->testRequirements();

        if($messages->has('error')){
            foreach($messages->get('error') as $message){
                $this->error($message);
            }
            foreach($messages->get('success') as $message){
                $this->line($message);
            }
            $this->error('Whoops, it looks like your system couldn\'t pass all of the requirements! Please address the error(s) and re-run the installer.');
            return;
        }else{
            foreach($messages->all() as $message){
                $this->line($message);
            }
            $this->info('Congratulations, your system passed all of the requirements!');
            if (!$this->confirm('Do you wish to continue? [y|N]', true)) {
                return;
            }
        }

        //get versions ready
        $versions = $this->installer->getVersions();

        //fetch any details needed
        $dataFilePath = $this->option('path');
        if(!is_null($dataFilePath)){
            if(!$this->filesystem->exists($dataFilePath)){
                $this->error($dataFilePath . ' doesn\'t exist, installer exiting!');
                return;
            }
            $values = collect(json_decode($this->filesystem->get($dataFilePath), true));
        }else{
            $values = collect([]);
            foreach($versions as $version){
                $tasks = $this->installer->getTasksForVersion($version);
                foreach($tasks as $task){
                    $fields = $task->getFields();
                    if(count($fields) > 0){
                        $this->info($task->getDescription());
                        foreach($fields as $field){
                            $value = $field->renderCommandField($this);
                            $values->put($field->getID(), $value);
                        }
                    }
                }
            }
        }

        $this->installer->setFieldValues($values->toArray());

        if($values->count() > 0){
            if ($this->confirm('Do you wish to review your information? [y|N]')) {
                $this->table(['Field', 'Value'], $values->map(function($item, $key){return [$key, $item];})->toArray());
            }

            if (!$this->confirm('Everything is set, do you want to continue and finish the install? [y|N]', true)) {
                return;
            }
        }

        foreach($versions as $version){
            $tasks = $this->installer->getTasksForVersion($version);
            foreach($tasks as $task){
                $this->info('Running ' . $task->getTitle() . ' Task...');
                try {
                    $task->setInput($this->installer->getFieldValues());
                    $task->run($this->getOutput());
                }catch(TaskRunException $e){
                    $this->error($e->getMessage());
                    return;
                }
                $this->info('Task ' . $task->getTitle() . ' Completed!');
            }
        }

        $this->dispatcher->fire(new AfterInstallEvent($this->installer, $this->getOutput()));

        $this->installer->saveCompleted();

        $this->info('Installation Completed!');

    }
}