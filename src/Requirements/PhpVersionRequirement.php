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

class PhpVersionRequirement extends Requirement implements RequirementInterface
{
    public $description = 'PHP version 5.5.0 or more is a requirement of this application.';

    public $success = 'The PHP version found is acceptable.';

    public $error = 'The PHP version found is lower than is required!';

    public function test(){
        return version_compare(PHP_VERSION, '5.5.9', '>=');
    }

}