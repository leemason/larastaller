## Larastaller Package
[![Packagist License](https://poser.pugx.org/leemason/larastaller/license.png)](http://choosealicense.com/licenses/mit/)
[![Latest Stable Version](https://poser.pugx.org/leemason/larastaller/version.png)](https://packagist.org/packages/leemason/larastaller)
[![Total Downloads](https://poser.pugx.org/leemason/larastaller/d/total.png)](https://packagist.org/packages/leemason/larastaller)
[![Build Status](https://travis-ci.org/leemason/larastaller.svg?branch=master)](https://travis-ci.org/leemason/larastaller)

The Larastaller package provides a fluent interface via web or artisan to install/upgrade your project.

## Installation

Require this package with composer:

```
composer require leemason/larastaller
```

After updating composer, add the ServiceProvider to the providers array in config/app.php

## Laravel 5.1:

```php
LeeMason\Larastaller\LarastallerServiceProvider::class,
```

Copy the packages config to your config folder with the publish command:

```php
php artisan vendor:publish --force
```

Once completed you should now have a config file located at config/larastaller.php.

In this file you will see 2 array items, ```requirements``` and ```versions```.

Both are arrays, requirements will contain some basic laravel required items to get you started (you may not need any more).

But for versions you will need to add your own. Each version can have the following:

```php
'1.0.0' => [
    'changes' => [
        'this is a change',
        'this is another'
    ],
    'requirements' => [
        //.. the same as the main requirements
    ],
    'tasks' => [
        \LeeMason\Larastaller\Tasks\AppKeyTask::class,
        \LeeMason\Larastaller\Tasks\MigrateTask::class,
        //.. any more tasks you need to complete for this version
    ],
]
```

As you can see the array key is the version string. This must be formatted in a way compatible with the php ```version_compare``` function.

## Compatability

The Larastaller package has been developed with Laravel 5.1, i see no reason why it wouldn't work with 5.0 or even 4 but it is only tested for 5.1.

## Introduction

@todo

## Usage

### http

@todo

### artisan

The larastaller package comes with multiple installer commands which are listed below:

```
php artisan installer:install
```

This will:

- Run through requirements for all versions, test them and report any errors.
- Request via console questions, passwords, choices and true/false fields all of the data requested by each task**
- Resolve each task class and perform the tasks ```handle()``` function
- If a task throws and exception, the install will cease
- If all tasks complete successfully save the installation details into the ```storage_path('installation.json');``` file
- Report the install as a success and exit

** This wont happen if you provide the option ```--path``` which can point to a json encoded file of key > value pairs for input values.
This is especially useful during a deployment process where ```--no-interaction``` is used.

```
php artisan installer:changes $version
```

```$version```` Possible values ["latest", "last-x" eg (last-5), "version" string eg (1.1.1), "all"]

This will fetch the version(s) requested and display the changes added to the version array as a list.


## Configuration

Included with the package is a config/larastaller.php file where the base config is loaded from, with an empty versions array.

This (after following the install steps above) gets copied to your applications config folder and should contain all of your custom requirements and versions.

The first key in the array is the list of requirements for installation (independent of any version).

Included with the package are some requirement classes which provide requirements that must be fulfilled for any laravel application, but if you have other requirements specific to your app you should append them here.

```php
'requirements' => [
    \LeeMason\Larastaller\Requirements\PhpVersionRequirement::class,
    \LeeMason\Larastaller\Requirements\PdoRequirement::class,
    \LeeMason\Larastaller\Requirements\MbStringRequirement::class,
    \LeeMason\Larastaller\Requirements\OpenSSLRequirement::class,
    \LeeMason\Larastaller\Requirements\TokenizerRequirement::class,
    \LeeMason\Larastaller\Requirements\FolderPermissionsRequirement::class,
    \LeeMason\Larastaller\Requirements\EnvFileRequirement::class,
],
```

The second item is the versions array, this contains an array of versions for the application.

Each version should be added as an index in the array with the version details as the keys value.

```php
'versions' => [
    '1.0.0' => [
        'changes' => [
            'this is a change'
        ],
        'requirements' => [

        ],
        'tasks' => [
            \LeeMason\Larastaller\Tasks\AppKeyTask::class,
            \LeeMason\Larastaller\Tasks\MigrateTask::class,
        ],
    ],
    '1.0.1' => [
        //..
    ],
],
```

Each version should contain an array of changes, and array of requirements (useful if server requirements change per version), and a tasks array.

## The Definition Class

The ```LeeMason\Larastaller\Definition``` class is quite simply a data storage object which allows more fluent access to the configuration, and news up ```LeeMason\Laratsaller\Version``` instances for each version.

It has a public api, but is designed soley for insternal usage via the Larastaller package.


## Requirement Classes

Requirement Classes are used to test the environment is suitable for the application to be installed.

They must implement the ```LeeMason\Larastaller\RequirementInterface``` and ideally extends the abstract ```LeeMason\Larastaller\Requirement``` class.

Each requirement class should contain the public properties ```$description```, ```$success```, ```$error``` with descriptive strings explain the requirement and providing user friendly messages.

A requirement class can optionally include a ```__construct()``` method to inject any dependencies via the ServiceContainer and these will automatically be resolved.

The last and most important method a requirement class **MUST** provide is a ```test()``` method which should return a boolean true/false depending on if the requirement has been fulfilled.

Most requirements just test the existence of the requirement, but the ```EnvFileRequirement``` is a special case as this will automatically copy the .env.example to .env if no .env file is present before testing.

This was chosen over making it a task simply because of a laravel application lifecycle.


## Task Classes

Task Classes are used to perform the tasks needed to install the application.

They must implement the ```LeeMason\Larastaller\TaskInterface``` and ideally extends the abstract ```LeeMason\Larastaller\Task``` class.

Each task class should contain the public property ```$title``` with a descriptive string explaining the tasks function.

A task class can optionally include a ```__construct()``` method to inject any dependencies via the ServiceContainer and these will automatically be resolved.

For the task to actually perform anything it should include a ```handle()``` method.

This method can do anything from running artisan commands, to altering database tables.

If the task should provide any user feedback messages during execution it can do so by using its ```$this->output``` property which will always be in stance of ```Symfony\Component\Console\Output\OutputInterface```.

This allows the task to add output by using the ```$this->output->writeLn('...');``` method.

Included with the package are some basic tasks to get you started:

```
LeeMason\Larastaller\Tasks\AppKeyTask
```

This task is very simple and performs the ``` php artisan key:generate``` command, returning its output.

We suggest this task be the first task registered for the first version you include.


```
LeeMason\Larastaller\Tasks\OptimizeTask
```

This task is very simple and performs the ``` php artisan clear-compiled && php artisan optimize --force``` command, returning its output.

We suggest this task be the last task registered for the every version you include because whenever someone installs or updates you can regenerate the compiled classes file.



```
LeeMason\Larastaller\Tasks\MigrateTask
```

This task on its own will simply perform the same function as calling ``` php artisan migrate```, so it is a useful task that can be added to any version which adds migrations to your application.

However its also been designed specifically with extendability in mind.

For example if you need to migrate a package, or provide options to the migrate command, you can extend the class and add your parameters to the ```$parameters``` array.

```php
use LeeMason\Larastaller\Tasks\MigrateTask;

class MigratePackageTask extends MigrateTask{

    protected $parameters = [
        '--path' => 'vendor/name/package/migrations'
    ];

}
```

This will run: ``` php artisan migrate --path=vendor/name/package/migrations```


## The Installation Class

The ```LeeMason\Larastaller\Installation``` class is a class responsible for fetching and updating the applications ```storage_path('installation.json')``` file.

It extends the ```Illuminate\Support\Collection``` class and all items from the ```storage_path('installation.json')``` file are stored as key > value items.

This class can provide and save data about the installation and it used by the ```LeeMason\Larastaller\Installer``` class for checking if the appliction is installed, and if it is up to date.

It is registered in the service container so can be injected and provides the public methods: ```$installation->isInstalled()```, ```$installation->isUpdated()``` on top of the Collection classes methods.

If you need to persist changes or additions to the installation.json file always make sure you call the ```$installation->save()``` method.


## The Installer Class

The ```LeeMason\Larastaller\Installer``` class is responsible for matching installed version against versions declared and generally formatting the configuration of tasks, requirements, etc for use via the http and command based install methods.

It provides no public api usable outside of its duties.


## Events

The package provides you with many events which can be hooked into to provide additional functionality:

```php
LeeMason\Larastaller\Events\AfterInstallEvent(Installer $installer, OutputInterface $output)
```

The above event is fired before the install process and is passed the Installer instance ```$event->installer```, and an OutputInterface ```$event->output``` allowing you to send messages back to the installer.


```php
LeeMason\Larastaller\Events\BeforeInstallEvent(Installer $installer, OutputInterface $output)
```

The above event is fired after the install process is completed and is passed the Installer instance ```$event->installer```, and an OutputInterface ```$event->output``` allowing you to send messages back to the installer.


## FAQ

**Why not just use version control?**

Version control is great, and the way we have designed the configuration for the package to best use the features of version control.

But services like git/svn etc can't for example:

- Clear caches on updates
- Modify database data between versions
- Alert to the need of new env vars
- etc

Sure each one of the those can be done manually, or even via multiple artisan commands, but the user needs to know those changes are needed.

With the larastaller config saved in version control anyone who needs to install it can just run ```installer:install``` without a task list in front of them.

**Does this replace the need for composer?**

No it doesn't and we wouldn't want to either.

What it does mean is you can distribute your application with one simple step.

Simply add ```"php artisan installer:install"``` to your composer.json files ```"post-install-command"``` array.

Then the install proccess will happen after ```composer install```.

** If you would prefer to use the web api simply distribute your application with the vendor directory propigated and direct them to the install url.


## Notes

Its still in early development, functionality yet to be included is:

Web api for installing
Updating existing installs
Events for install/update


## The Future

- Add web api/route
- Add installer:update command
- Minor functionality improvements