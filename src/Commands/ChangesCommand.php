<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 02/11/15
 * Time: 16:59
 */

namespace LeeMason\Larastaller\Commands;


use Illuminate\Console\Command;
use LeeMason\Larastaller\Definition;

class ChangesCommand extends Command
{

    protected $signature = 'installer:changes {version=latest : Possible values ["latest", "last-x" eg (last-5), "version" string eg (1.1.1), "all"]}';

    protected $description = 'Displays application changes';

    private $definition;

    public function __construct(Definition $definition)
    {
        parent::__construct();
        $this->definition = $definition;
    }

    public function handle()
    {

        $requested = $this->argument('version');
        if($requested == 'all') {
            $versions = $this->definition->getVersions()->reverse();
        }elseif($requested == 'latest') {
            $versions = collect([$this->definition->getLatestVersion()]);
        }elseif(str_contains($requested, 'last-')) {
            $amount = last(explode('-', $requested));
            $versions = $this->definition->getVersions();
            $versions = $versions->slice(($versions->count() - $amount))->reverse();
        }else{
            $versions = collect([$this->definition->getVersions()->get($requested)]);
        }

        foreach($versions as $version){
            $this->info('Version ' . $version->version);
            $changes = $version->getChanges();
            foreach($changes as $change){
                $this->comment('* ' . $change);
            }
        }
    }
}