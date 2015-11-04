<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 02/11/15
 * Time: 16:59
 */

namespace LeeMason\Larastaller\Commands;


use Illuminate\Console\Command;
use LeeMason\Larastaller\Installer;
use LeeMason\Larastaller\TaskRunException;

class InstallCommand extends Command
{

    protected $signature = 'installer:install';

    protected $description = 'Installs the application';

    private $installer;


    public function __construct(Installer $installer)
    {
        parent::__construct();
        $this->installer = $installer;
    }

    public function handle()
    {
        if($this->installer->getInstallation()->isInstalled()){
            $this->error('Your application is already installed!');
            return;
        }

        $bar = $this->output->createProgressBar((6 + $this->installer->getTotalSteps()));

        $bar->advance();$this->info('');//fix line break

        $this->info('Welcome to the installer!');

        //test requirements
        $bar->advance();$this->info('');//fix line break
        $this->info('Checking Requirements...');

        $messages = $this->installer->testRequirements();

        if($messages->has('error')){
            foreach($messages->get('error') as $message){
                $this->error($message);
            }
            foreach($messages->get('success') as $message){
                $this->line($message);
            }
            $this->error('Whoops, it looks like your system couldn\'t pass all of the requirements! Please address the error and re-run the installer.');
            return;
        }else{
            foreach($messages->all() as $message){
                $this->line($message);
            }
            $this->info('Congratulations, your system passed all of the requirements!');
            if (!$this->confirm('Do you wish to continue? [y|N]')) {
                return;
            }
            $bar->advance();$this->info('');//fix line break
        }

        //fetch any details needed
        $values = collect([]);
        $versions = $this->installer->getVersions();

        foreach($versions as $version){
            $tasks = $this->installer->getTasksForVersion($version);
            foreach($tasks as $task){
                $fields = $task->getFields();
                if(count($fields) > 0){
                    $this->info($task->getDescription());
                    foreach($fields as $field){
                        $value = $field->renderCommandField($this);
                        $values->put($field->getID(), $value);
                        $bar->advance();
                        $this->info('');//fix line break
                    }
                }
            }
        }

        $this->installer->setFieldValues($values->toArray());

        if($values->count() < 1){
            if ($this->confirm('Do you wish to review your information? [y|N]')) {
                $this->table(['Field', 'Value'], $values->map(function($item, $key){return [$key, $item];})->toArray());
            }
            $bar->advance();$this->info('');//fix line break

            if (!$this->confirm('Everything is set, do you want to continue and finish the install? [y|N]')) {
                return;
            }
            $bar->advance();$this->info('');//fix line break
        }else{
            $bar->advance();$this->info('');//fix line break
            $bar->advance();$this->info('');//fix line break
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
                $bar->advance();$this->info('');//fix line break
                $this->info('Task ' . $task->getTitle() . ' Completed!');
            }
        }

        $this->installer->saveCompleted();

        $bar->finish();$this->info('');//fix line break

        $this->info('Installation Completed!');

    }
}