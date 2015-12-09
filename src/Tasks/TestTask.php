<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 02/11/15
 * Time: 14:35
 */

namespace LeeMason\Larastaller\Tasks;


use Illuminate\Contracts\Console\Kernel;
use LeeMason\Larastaller\Fields\TextField;
use LeeMason\Larastaller\Task;
use LeeMason\Larastaller\TaskInterface;

class TestTask extends Task implements TaskInterface
{
    public $title = 'Test Task';

    private $artisan;

    public $description = 'In this task we need some details from you!';

    public function __construct(Kernel $artisan){
        $this->artisan = $artisan;
    }

    public function defineFields(){
        return [
          new TextField([
              'label' => 'test text field',
              'description' => 'This is the desc',
              'validate' => 'required'
          ])
        ];
    }

    public function handle()
    {
        $this->artisan->call('view:clear');
        foreach (array_filter(explode("\n", $this->artisan->output())) as $line){
            $this->output->writeLn($line);
        }
    }

}