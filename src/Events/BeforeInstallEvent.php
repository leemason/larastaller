<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 06/11/15
 * Time: 17:24
 */

namespace LeeMason\Larastaller\Events;


use LeeMason\Larastaller\Installer;
use Symfony\Component\Console\Output\OutputInterface;

class BeforeInstallEvent
{

    public $installer;

    public $output;

    public function __construct(Installer $installer, OutputInterface $output){
        $this->installer = $installer;
        $this->output = $output;
    }

}