<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 02/11/15
 * Time: 13:50
 */

namespace LeeMason\Larastaller;


use Illuminate\Contracts\Foundation\Application;

class Definition
{

    use HasRequirements;

    private $versions = [];

    public function __construct(){
        $this->versions = collect([]);
        $this->requirements = collect([]);
    }

    public function addVersion(Version $version){
        $this->versions->put($version->version, $version);
        $this->versions = $this->versions->sort();
        return $this;
    }

    public function forVersion($version){
        $obj = $this->versions->get($version);
        if(is_null($obj)){
            $this->addVersion(new Version($version));
            return $this->forVersion($version);
        }
        return $obj;
    }

    /**
     * @return array|\Illuminate\Support\Collection
     */
    public function getVersions()
    {
        return $this->versions;
    }

    public function getLatestVersion(){
        return $this->versions->last();
    }

}