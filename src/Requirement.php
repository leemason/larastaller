<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 02/11/15
 * Time: 14:05
 */

namespace LeeMason\Larastaller;


abstract class Requirement implements RequirementInterface
{

    public $description;

    public $success;

    public $error;

    public function getDescription()
    {
        return $this->description;
    }

    public function getSuccess()
    {
        return $this->success;
    }

    public function getError()
    {
        return $this->error;
    }

    public function test()
    {
        return false;
    }

}