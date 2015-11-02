<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 02/11/15
 * Time: 13:46
 */

namespace LeeMason\Larastaller;


use Illuminate\Support\ServiceProvider;
use LeeMason\Larastaller\Commands\InstallCommand;
use LeeMason\Larastaller\Requirements\MbStringRequirement;
use LeeMason\Larastaller\Requirements\OpenSSLRequirement;
use LeeMason\Larastaller\Requirements\PdoRequirement;
use LeeMason\Larastaller\Requirements\PhpVersionRequirement;
use LeeMason\Larastaller\Requirements\TokenizerRequirement;
use LeeMason\Larastaller\Tasks\MigrateTask;

class LarastallerServiceProvider extends ServiceProvider
{

    public function register(){

        //responsible for defining the versions and actions
        $this->app->singleton(Definition::class, function($app){
            return new Definition();
        });

        //responsible for installing the app against the definition
        $this->app->singleton(Installer::class, function($app){
            return new Installer($app[Definition::class], $app[Installation::class]);
        });

        //responsible for fetching/setting installation data
        $this->app->singleton(Installation::class, function($app){
            return new Installation($app['files'], $app[Definition::class]);
        });

    }

    public function boot(Definition $definition, Installer $installer, Installation $installation){

        $definition->addRequirement(PhpVersionRequirement::class);
        $definition->addRequirement(PdoRequirement::class);
        $definition->addRequirement(MbStringRequirement::class);
        $definition->addRequirement(OpenSSLRequirement::class);
        $definition->addRequirement(TokenizerRequirement::class);


        $version = new Version('1.0.0');
        $version->addChange('this is a change for v1.0.0');
        $version->addTask(MigrateTask::class);

        $definition->addVersion($version);

        if(!$installation->isInstalled()) {
            //register install command
            if($this->app->runningInConsole()) {
                $this->commands([InstallCommand::class]);
            }else{
                //run the installer
                $installer->run();
            }
        }elseif(!$installation->isUpdated()){
            //run upgrader
        }

    }

}