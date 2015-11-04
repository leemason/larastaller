<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 02/11/15
 * Time: 14:14
 */

namespace LeeMason\Larastaller;


use Illuminate\Support\Collection;
use Symfony\Component\Console\Output\OutputInterface;

interface TaskInterface
{

    public function getDescription();

    public function getSuccess();

    public function getError();

    public function getFields();

    public function getField($id);

    public function setInput(Collection $input);

    public function run(OutputInterface $output);

    public function handle();

}