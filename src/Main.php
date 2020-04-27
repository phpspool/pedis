<?php
declare(strict_types=1);
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Spool\Pedis;
use Spool\Pedis\Lib\Config;
use Spool\Pedis\Lib\BootStrap;

class Main
{
    public function __construct(string $configFile = '')
    {
        $config = new Config($configFile);
        $bootStrap = new BootStrap($config);
        $bootStrap->Run();
    }
}
