<?php

declare(strict_types = 1);

namespace Spool\Pedis\Tests;

use PHPUnit\Framework\TestCase;

use Spool\Pedis\Lib\Config;
class ExampleTest extends TestCase
{

    public function testEmpty()
    {
        $tmp = function(){};
        var_dump($tmp, is_callable($tmp));
        $this->assertTrue(TRUE);
    }
}
