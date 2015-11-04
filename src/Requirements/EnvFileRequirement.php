<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 02/11/15
 * Time: 14:30
 */

namespace LeeMason\Larastaller\Requirements;


use Illuminate\Filesystem\Filesystem;
use LeeMason\Larastaller\Requirement;
use LeeMason\Larastaller\RequirementInterface;

class EnvFileRequirement extends Requirement implements RequirementInterface
{
    public $description = 'The application requires a .env file to placed in its root folder.';

    public $success = 'The applications .env is present or has been created for you.';

    public $error = 'The applications .env file isn\'t present, or we don\'t have permissions to create\read it.';

    private $filesystem;

    public function __construct(Filesystem $filesystem){
        $this->filesystem = $filesystem;
    }

    public function test(){
        $path = base_path('.env');
        if($this->filesystem->isFile($path)){
            return true;
        }
        $this->filesystem->copy(base_path('.env.example'), $path);
        if($this->filesystem->isFile($path)){
            return true;
        }
        return false;
    }

}