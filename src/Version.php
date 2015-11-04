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

    public function setChanges($changes)
    {
        $this->changes = collect($changes);
    }

    public function getChanges()
    {
        return $this->changes;
    }

    public function setTasks($tasks){
        $this->tasks = collect($tasks);
    }

    public function getTasks()
    {
        return $this->tasks;
    }

}