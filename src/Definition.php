<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 02/11/15
 * Time: 13:50
 */

namespace LeeMason\Larastaller;


class Definition
{

    use HasRequirements;

    private $versions = [];

    public function __construct(){
        $this->versions = collect([]);
        $this->requirements = collect([]);
    }

    public function getAllRequirements(){
        $requirements = $this->getRequirements();
        $versions = $this->getVersions();
        foreach($versions as $version){
            $requirements = $requirements->merge($version->getRequirements()->toArray());
        }
        return $requirements;
    }

    public function getVersions()
    {
        return $this->versions;
    }

    public function setVersions($versions = []){
        $this->versions = collect($versions)->map(function($value, $version){
            $obj = new Version($version);
            $obj->setChanges($value['changes']);
            $obj->setRequirements($value['requirements']);
            $obj->setTasks($value['tasks']);
            return $obj;
        });
    }

    public function getLatestVersion(){
        return $this->versions->last();
    }

}