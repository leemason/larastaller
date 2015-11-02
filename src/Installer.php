<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 02/11/15
 * Time: 15:27
 */

namespace LeeMason\Larastaller;

use Illuminate\Support\MessageBag;

class Installer
{

    private $definition;

    private $installation;

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

    public function getRequirements(){
        $requirements = $this->definition->getRequirements();
        $versions = $this->definition->getVersions();
        foreach($versions as $version){
            $requirements = $requirements->merge($version->getRequirements()->toArray());
        }
        return $requirements;
    }

    public function testRequirements(){
        $messages = new MessageBag();
        $requirements = $this->getRequirements();
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
        return $this->definition->getVersions();
    }

    public function getTasksForVersion($version){
        $tasks = $version->getTasks();
        $tasks = $tasks->map(function($task){
            if($task instanceof TaskInterface){
                return $task;
            }
            return app($task);
        });
        $version->setTasks($tasks);
        return $tasks;
    }

    public function run(){

    }
}