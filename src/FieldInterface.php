<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 02/11/15
 * Time: 18:46
 */

namespace LeeMason\Larastaller;


use Illuminate\Console\Command;

interface FieldInterface
{
    public function __construct(array $attributes = []);

    public function getValidator($value);

    public function getLabel();

    public function getConsoleLabel();

    public function getDescription();

    public function renderCommandField(Command $command);

}