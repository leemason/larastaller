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

class OpenSSLRequirement extends Requirement implements RequirementInterface
{

    public $description = 'The openssl extension is a requirement of this application.';

    public $success = 'The openssl extension is present.';

    public $error = 'The openssl extension cannot be found!';

    public function test(){
        return extension_loaded('openssl');
    }

}