<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 04/11/15
 * Time: 18:40
 */

namespace LeeMason\Larastaller\Fields;


use Illuminate\Console\Command;
use LeeMason\Larastaller\Field;
use LeeMason\Larastaller\FieldInterface;

class ConfirmationField extends Field implements FieldInterface
{
    public function renderCommandField(Command $command){
        while(true){
            $input = $command->confirm($this->getConsoleLabel());
            $validator = $this->getValidator($input);
            if($validator->passes()){
                return $input;
            }else{
                $command->error($validator->errors()->first());
            }
        }
    }
}