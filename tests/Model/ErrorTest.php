<?php

namespace MocOrm\Tests;

use MocOrm\Model\Error;

final class ErrorTest extends \PHPUnit\Framework\TestCase
{
    protected static $error;

    public function testFirstError()
    {
        self::$error = Error::create('First error', 42);
        $this->assertInstanceOf(Error::class, self::$error);

        self::$error = Error::create('Another error message', 2);
        $this->assertInstanceOf(Error::class, self::$error);

        $erro = self::$error->getFirst();
        $this->assertEquals('First error', $erro['message']);
        $this->assertEquals(42, $erro['code']);
    }

    public function testLastError()
    {
        self::$error = Error::create('Last error', 69);
        $this->assertInstanceOf(Error::class, self::$error);
        
        $erro = self::$error->getLast();
        $this->assertEquals('Last error', $erro['message']);
        $this->assertEquals(69, $erro['code']);
    }

    public function testGetAll()
    {
        $all = self::$error->getAll();
        $this->assertEquals(3, count($all));
    }
}
