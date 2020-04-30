<?php

declare(strict_types = 1);

namespace Spool\Pedis\Tests;

use PHPUnit\Framework\TestCase;

use Spool\Pedis\Exceptions\PedisException;
use Spool\Pedis\Constants\ErrorCode;
use Spool\Pedis\Lib\Config;
use Spool\Pedis\Main;
class ExampleTest extends TestCase
{

    public function testEmpty()
    {
//        throw new PedisException(ErrorCode::getMessage(ErrorCode::CANNOT_SELECT_LARGER_THAN_THE_CONFIGURATION_DATABASE));
        $tmp = function(){};
//        var_dump($tmp, is_callable($tmp));
        $this->assertTrue(TRUE);
    }
    
    public function testRun()
    {
        $filename = '/var/pedis/run/pedis_9736.pid';
        if (is_file($filename)) {
            unlink($filename);
        }
        $main = new Main();
        $main->Run();
        $this->assertTrue(TRUE);
    }
}
