<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 02/11/15
 * Time: 14:05
 */

namespace LeeMason\Larastaller;


interface RequirementInterface
{

    public function getDescription();

    public function getSuccess();

    public function getError();

    public function test();

}