<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 02/11/15
 * Time: 13:46
 */

namespace LeeMason\Larastaller;


use Illuminate\Support\ServiceProvider;
use LeeMason\Larastaller\Commands\ChangesCommand;
use LeeMason\Larastaller\Commands\InstallCommand;

class LarastallerServiceProvider extends ServiceProvider
{

    public function register(){

        $configPath = __DIR__ . '/../config/larastaller.php';
        $this->mergeConfigFrom($configPath, 'larastaller');

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

        $configPath = __DIR__ . '/../config/larastaller.php';
        $this->publishes([$configPath => config_path('larastaller.php')], 'config');

        $definition->setRequirements(config('larastaller.requirements'));
        $definition->setVersions(config('larastaller.versions'));

        $this->commands([InstallCommand::class, ChangesCommand::class]);


        if(!$installation->isInstalled()) {
            //run the installer
            $installer->run();
        }elseif(!$installation->isUpdated()){
            //run upgrader
        }

    }

}