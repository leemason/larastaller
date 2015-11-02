<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 02/11/15
 * Time: 19:05
 */

namespace LeeMason\Larastaller\Fields;


use Illuminate\Console\Command;
use LeeMason\Larastaller\Field;
use LeeMason\Larastaller\FieldAttemptsExceededException;
use LeeMason\Larastaller\FieldInterface;

class TextField extends Field implements FieldInterface
{
    public function renderCommandField(Command $command){
        while(true){
            $input = $command->ask($this->get('label'), $this->get('default', ''));
            $validator = $this->getValidator($input);
            if($validator->passes()){
                $this->input = $input;
                return;
            }else{
                $command->error($validator->errors()->first());
            }
        }
    }

}