<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 02/11/15
 * Time: 14:14
 */

namespace LeeMason\Larastaller;


interface TaskInterface
{

    public function getDescription();

    public function getSuccess();

    public function getError();

    public function getFields();

    public function getField($id);

    public function run();

    public function handle();

}