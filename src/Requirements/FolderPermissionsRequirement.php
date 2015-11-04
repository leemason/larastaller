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

class FolderPermissionsRequirement extends Requirement implements RequirementInterface
{
    public $description = 'The storage folder and its children for this application must be writable by the web server.';

    public $success = 'The storage folder and its children have the correct permissions.';

    public $error = 'The storage folder and its children do not have 755 permissions, please use the chmod(0755) command to rectify!';

    private $filesystem;

    public function __construct(Filesystem $filesystem){
        $this->filesystem = $filesystem;
    }

    public function test(){
        $path = storage_path();
        if(!$this->filesystem->exists($path) || !$this->filesystem->isDirectory($path) || !$this->filesystem->isWritable($path)){
            return false;
        }
        $directories = $this->filesystem->directories($path);
        foreach($directories as $directory){
            if(!$this->filesystem->isWritable($directory)){
                return false;
            }
        }

        return true;
    }

}