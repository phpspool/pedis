<?php

declare(strict_types = 1);

namespace Spool\Pedis\Tests;

use PHPUnit\Framework\TestCase;

use Spool\Pedis\Lib\Config;
class ExampleTest extends TestCase
{

    public function testEmpty()
    {
        $filename = __DIR__ . '/../config/config.ini';
        $config = new Config($filename);
        var_dump($config);
        $this->assertTrue(TRUE);
    }
}
