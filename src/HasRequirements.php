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

    public function getRequirements()
    {
        return $this->requirements;
    }

    public function setRequirements($requirements = [])
    {
        $this->requirements = collect($requirements);
    }

}