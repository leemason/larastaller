<?php

namespace LeeMason\Larastaller;


use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use LeeMason\Larastaller\Commands\ChangesCommand;
use LeeMason\Larastaller\Commands\InstallCommand;

class LarastallerServiceProvider extends ServiceProvider
{

    /**
     * Register the Debugbar Middleware
     *
     * @param  string $middleware
     */
    protected function registerMiddleware($middleware)
    {
        $kernel = $this->app['Illuminate\Contracts\Http\Kernel'];
        $kernel->pushMiddleware($middleware);
    }

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

    public function boot(Definition $definition, Installation $installation, Router $router){

        $configPath = __DIR__ . '/../config/larastaller.php';
        $this->publishes([$configPath => config_path('larastaller.php')], 'config');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'larastaller');

        $definition->setRequirements(config('larastaller.requirements'));
        $definition->setVersions(config('larastaller.versions'));

        $this->commands([InstallCommand::class, ChangesCommand::class]);


        if(!$installation->isInstalled()) {
            //redirect none install requests to the installer
            $this->registerMiddleware('LeeMason\Larastaller\Middlewares\InstallMiddleware');
            $router->get('install', ['as' => 'installer.get', 'uses' => Http\Controllers\InstallController::class . '@getInstall']);
            $router->post('install/validate', ['as' => 'installer.validate', 'uses' => Http\Controllers\InstallController::class . '@postInstallValidate']);
            $router->post('install', ['as' => 'installer.post', 'uses' => Http\Controllers\InstallController::class . '@postInstall']);
        }elseif(!$installation->isUpdated()){
            //redirect none update requests to the installer
            $this->registerMiddleware('LeeMason\Larastaller\Middlewares\UpdateMiddleware');
            $router->get('update', function(){
                return 'updater';
            });
        }

    }

}