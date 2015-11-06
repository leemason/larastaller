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

class OptimizeTask extends Task implements TaskInterface
{
    public $title = 'App Optimize';

    private $artisan;

    public function __construct(Kernel $artisan){
        $this->artisan = $artisan;
    }

    public function handle()
    {
        $this->artisan->call('clear-compiled');
        $this->artisan->call('optimize', ['--force' => true]);
        foreach (array_filter(explode("\n", $this->artisan->output())) as $line){
            $this->output->writeLn($line);
        }
    }

}