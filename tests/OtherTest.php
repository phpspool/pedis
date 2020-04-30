<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Spool\Pedis\Tests;

use PHPUnit\Framework\TestCase;
use Spool\Pedis\Lib\Log;
/**
 * Description of OtherTest
 *
 * @author 陈浩波
 */
class OtherTest extends TestCase
{
    public function testFD()
    {
        echo "fd: ", (int)STDOUT, "\n";
        $this->assertTrue(true);
    }
    public function testBaseFile()
    {
        echo "rootPath: ", PEDIS_ROOT, "\n";
        $this->assertTrue(true);
    }
    
    public function testLogInfo()
    {
        Log::test();
        $this->assertTrue(true);
    }
}
