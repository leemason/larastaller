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
use LeeMason\Larastaller\FieldInterface;

class ChoiceField extends Field implements FieldInterface
{
    public function renderCommandField(Command $command){
        while(true){
            $input = $command->choice($this->getConsoleLabel(), $this->get('choices', []) , $this->get('default', null));
            $validator = $this->getValidator($input);
            if($validator->passes()){
                return $input;
            }else{
                $command->error($validator->errors()->first());
            }
        }
    }

}