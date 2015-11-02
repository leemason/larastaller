<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 02/11/15
 * Time: 17:37
 */

namespace LeeMason\Larastaller;


use Illuminate\Support\Collection;

trait HasRequirements
{
    public $requirements = [];

    public function addRequirement($requirement){
        if(!$this->requirements instanceof Collection){
            $this->requirements = collect([]);
        }
        $this->requirements->push($requirement);
    }

    public function getRequirements()
    {
        return $this->requirements;
    }

}