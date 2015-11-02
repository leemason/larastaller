<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 02/11/15
 * Time: 14:35
 */

namespace LeeMason\Larastaller\Tasks;


use Illuminate\Support\Facades\Artisan;
use LeeMason\Larastaller\Fields\TextField;
use LeeMason\Larastaller\Task;
use LeeMason\Larastaller\TaskInterface;
use LeeMason\Larastaller\TaskRunException;

class MigrateTask extends Task implements TaskInterface
{
    public $title = 'Database Migration';

    public $description = 'desc';

    public $success = 'success';

    public $error = 'error';

    public function defineFields(){
        return [
            new TextField([
                'label' => 'username',
                'validate' => 'required'
            ])
        ];
    }

    public function handle(){
        Artisan::call('migrate');
        //throw new TaskRunException('an error occured');
    }

}