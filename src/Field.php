<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 02/11/15
 * Time: 18:46
 */

namespace LeeMason\Larastaller;


use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Fluent;

abstract class Field extends Fluent implements FieldInterface
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }


    public function getLabel()
    {
        return $this->get('label', '');
    }

    public function getConsoleLabel(){
        return $this->get('console_label', $this->getLabel());
    }

    public function getDescription()
    {
        return $this->get('description', '');
    }


    public function getValidator($value)
    {
        $validator = Validator::make(
            [
                'value' => $value
            ],
            [
                'value' => $this->get('validate', '')
            ]
        );
        return $validator;
    }

    public function getID(){
        return snake_case($this->get('label'));
    }

}