<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 02/11/15
 * Time: 15:36
 */

namespace LeeMason\Larastaller;


use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;

class Installation extends Collection
{
    private $filesystem;

    private $definition;

    private $path;

    public function __construct(Filesystem $filesystem, Definition $definition){
        $this->filesystem = $filesystem;
        $this->definition = $definition;
        $this->path = storage_path('installation.json');
        $this->fetchData();
    }

    private function fetchData(){
        if($this->filesystem->exists($this->path)){
            $this->items = $this->getArrayableItems(json_decode($this->filesystem->get($this->path)));
        }
    }

    public function save(){
        $this->filesystem->put($this->path, $this->toJson());
    }

    public function isInstalled(){
        return $this->get('installed', false);
    }

    public function isUpdated(){
        $latestVersion = $this->definition->getLatestVersion();
        return $this->get('version', '0.0.0') == $latestVersion->version;
    }

}