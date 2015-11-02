<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 02/11/15
 * Time: 14:30
 */

namespace LeeMason\Larastaller\Requirements;


use LeeMason\Larastaller\Requirement;
use LeeMason\Larastaller\RequirementInterface;

class MbStringRequirement extends Requirement implements RequirementInterface
{
    public $description = 'The mbstring extension is a requirement of this application.';

    public $success = 'The mbstring extension is present.';

    public $error = 'The mbstring extension cannot be found!';

    public function test(){
        return extension_loaded('mbstring');
    }

}