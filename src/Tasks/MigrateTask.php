<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 02/11/15
 * Time: 14:35
 */

namespace LeeMason\Larastaller\Tasks;


use Illuminate\Contracts\Console\Kernel;
use LeeMason\Larastaller\Task;
use LeeMason\Larastaller\TaskInterface;

class MigrateTask extends Task implements TaskInterface
{
    public $title = 'Database Migration';

    private $artisan;

    /*
     * An array of key -> values pairs to pass to the migrate command, for example ['--path' => 'vendor/leemason/polycast/migrations']
     */
    protected $parameters = [];

    public function __construct(Kernel $artisan){
        $this->artisan = $artisan;
    }

    public function handle()
    {
        $this->artisan->call('migrate', $this->parameters);
        foreach (array_filter(explode("\n", $this->artisan->output())) as $line){
            $this->output->writeLn($line);
        }
    }

}