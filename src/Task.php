<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 02/11/15
 * Time: 14:17
 */

namespace LeeMason\Larastaller;


use Illuminate\Support\Collection;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Task implements TaskInterface
{
    public $title;

    public $description;

    public $success;

    public $error;

    public $fields = [];

    public $fieldValues = [];

    public $input;

    public $output;

    public function getTitle()
    {
        return $this->title;
    }

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

    public function defineFields(){
        return [];
    }

    public function getFields(){
        if(empty($this->fields)){
            $this->fields = collect($this->defineFields());
        }
        return $this->fields;
    }

    public function getField($id){
        $fields = $this->getFields();
        return $fields->first(function($key, $field) use ($id){
            return $field->getID() == $id;
        });
    }

    public function setInput(Collection $input){
        $this->input = $input;
    }

    public function getInput()
    {
        return $this->input;
    }

    public function run(OutputInterface $output){
        $this->output = $output;
        $this->handle();
    }

}