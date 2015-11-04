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

The larastaller package comes with an install command which is access by:

``` php artisan installer:install```

This will:

- Run through requirements for all versions, test them and report any errors.
- Request via console questions, passwords, choices and true/false fields all of the data requested by each task
- Resolve each task class and perform the tasks ```handle()``` function
- If a task throws and exception, the install will cease
- If all tasks complete successfully save the installation details into the ```storage_path('installation.json');``` file
- Report the install as a success and exit



