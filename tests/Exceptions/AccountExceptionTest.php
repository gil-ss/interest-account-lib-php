<?php

namespace InterestAccountLibrary\Tests\Exceptions;

use PHPUnit\Framework\TestCase;
use InterestAccountLibrary\Exceptions\AccountException;

class AccountExceptionTest extends TestCase
{
    public function testExceptionMessage(): void
    {
        $exception = new AccountException('Test message');
        $this->assertEquals('Test message', $exception->getMessage());
    }

    public function testExceptionCode(): void
    {
        $exception = new AccountException('Test message', 123);
        $this->assertEquals(123, $exception->getCode());
    }

    public function testExceptionPrevious(): void
    {
        $previous = new \Exception('Previous exception');
        $exception = new AccountException('Test message', 0, $previous);
        $this->assertEquals($previous, $exception->getPrevious());
    }

    public function testExceptionIsInstanceOfException(): void
    {
        $exception = new AccountException('Test message');
        $this->assertInstanceOf(\Exception::class, $exception);
    }
}