<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 02/11/15
 * Time: 13:52
 */

namespace LeeMason\Larastaller;


class Version
{
    use HasRequirements;

    public $version = '';

    private $tasks = [];

    private $changes = [];

    public function __construct($version){
        $this->version = $version;
        $this->requirements = collect([]);
        $this->tasks = collect([]);
        $this->changes = collect([]);
    }

    public function addTask($task){
        $this->tasks->push($task);
    }

    public function addChange($change){
        $this->changes->push($change);
    }

    public function setChanges($changes)
    {
        $this->changes = collect($changes);
    }

    public function getChanges()
    {
        return $this->changes;
    }

    public function getTasks()
    {
        return $this->tasks;
    }

    public function setTasks($tasks){
        $this->tasks = $tasks;
    }


}