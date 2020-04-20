<?php

declare(strict_types = 1);

namespace Spool\Pedis\Tests;

use PHPUnit\Framework\TestCase;

use Spool\Pedis\Lib\Config;
class ExampleTest extends TestCase
{

    public function testEmpty()
    {
        $tmp = microtime(TRUE);
        var_dump($tmp);
        $this->assertTrue(TRUE);
    }
}
