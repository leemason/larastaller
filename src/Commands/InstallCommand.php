<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 02/11/15
 * Time: 16:59
 */

namespace LeeMason\Larastaller\Commands;


use Illuminate\Console\Command;
use LeeMason\Larastaller\FieldAttemptsExceededException;
use LeeMason\Larastaller\Installer;
use LeeMason\Larastaller\TaskRunException;

class InstallCommand extends Command
{

    protected $signature = 'install';

    protected $description = 'Install the application';

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
            $this->error('Whoops, it looks like your system couldn\'t pass all of the requirements! Please address the error and re-run the installer.');
        }else{
            foreach($messages->all() as $message){
                $this->line($message);
            }
            $this->info('Congratulations, your system passed all of the requirements!');
            if (!$this->confirm('Do you wish to continue? [y|N]')) {
                return;
            }
        }

        //fetch any details needed
        $tableData = collect([]);

        $versions = $this->installer->getVersions();
        foreach($versions as $version){
            $tasks = $this->installer->getTasksForVersion($version);
            foreach($tasks as $task){
                $fields = $task->getFields();
                if(count($fields) > 0){
                    $this->info($task->getDescription());
                    foreach($fields as $field){
                        $field->renderCommandField($this);
                        $tableData->push([$field->getLabel(), $field->get('input')]);
                    }
                }
            }
        }

        if ($this->confirm('Do you wish to review your information? [y|N]')) {
            $this->table(['Field', 'Value'], $tableData->toArray());
        }

        if (!$this->confirm('Everything is set, do you want to continue and finish the install? [y|N]')) {
            return;
        }

        foreach($versions as $version){
            $tasks = $this->installer->getTasksForVersion($version);
            foreach($tasks as $task){
                $this->info('Running ' . $task->getTitle() . ' Task...');
                try {
                    $task->run();
                }catch(TaskRunException $e){
                    $this->error($e->getMessage());
                    return;
                }
                $this->info($task->getTitle() . ' Completed!');
            }
        }

        $this->info('Installation Completed!');

    }
}