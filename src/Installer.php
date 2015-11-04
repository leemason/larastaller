<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 02/11/15
 * Time: 15:27
 */

namespace LeeMason\Larastaller;

use Carbon\Carbon;
use Illuminate\Support\MessageBag;

class Installer
{

    private $definition;

    private $installation;

    private $fieldValues = [];

    private $resolvedTasks = [];

    public function __construct(Definition $definition, Installation $installation){
        $this->definition = $definition;
        $this->installation = $installation;
    }

    /**
     * @return Definition
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * @return Installation
     */
    public function getInstallation()
    {
        return $this->installation;
    }

    public function testRequirements(){
        $messages = new MessageBag();
        $requirements = $this->getDefinition()->getAllRequirements();
        foreach($requirements as $requirement){
            $requirement = app($requirement);
            if($requirement->test()){
                $messages->add('success', $requirement->getSuccess());
            }else{
                $messages->add('error', $requirement->getError());
            }
        }
        return $messages;
    }

    public function getVersions(){
        return $this->getDefinition()->getVersions();
    }

    public function getTasksForVersion($version){
        if(isset($this->resolvedTasks[$version->version])){
            return $this->resolvedTasks[$version->version];
        }
        $tasks = $version->getTasks();
        $this->resolvedTasks[$version->version] = $tasks->map(function($task){
            if($task instanceof TaskInterface){
                return $task;
            }
            return app($task);
        });
        return $this->resolvedTasks[$version->version];
    }

    public function getTotalSteps(){
        return (
            $this->getTaskFieldsCount() +
            $this->getTaskCount()
        );
    }

    public function getTaskFieldsCount(){
        $count = 0;
        foreach($this->getVersions() as $version){
            $tasks = $this->getTasksForVersion($version);
            foreach($tasks as $task){
                $count += $task->getFields()->count();

            }
        }
        return $count;
    }

    public function getTaskCount(){
        $count = 0;
        foreach($this->getVersions() as $version){
            $count += $version->getTasks()->count();
        }
        return $count;
    }

    /**
     * @return array
     */
    public function getFieldValues()
    {
        return $this->fieldValues;
    }

    /**
     * @param array $taskFieldValues
     */
    public function setFieldValues($fieldValues = [])
    {
        $this->fieldValues = collect($fieldValues);
    }


    public function saveCompleted(){
        $this->installation->put('installed', true);
        $this->installation->put('installed_on', Carbon::now()->toDateTimeString());
        $this->installation->put('version', $this->definition->getLatestVersion()->version);
        $this->installation->save();
    }

    public function run(){

    }
}