<?php

namespace LeeMason\Larastaller;


trait HasRequirements
{
    public $requirements = [];

    public function getRequirements()
    {
        return $this->requirements;
    }

    public function setRequirements($requirements = [])
    {
        $this->requirements = collect($requirements);
    }

}