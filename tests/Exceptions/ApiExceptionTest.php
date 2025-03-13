<?php

namespace InterestAccountLibrary\Tests\Exceptions;

use PHPUnit\Framework\TestCase;
use InterestAccountLibrary\Exceptions\ApiException;

class ApiExceptionTest extends TestCase
{
    public function testExceptionMessage(): void
    {
        $exception = new ApiException('Test message');
        $this->assertEquals('Test message', $exception->getMessage());
    }

    public function testExceptionCode(): void
    {
        $exception = new ApiException('Test message', 123);
        $this->assertEquals(123, $exception->getCode());
    }

    public function testExceptionPrevious(): void
    {
        $previous = new \Exception('Previous exception');
        $exception = new ApiException('Test message', 0, $previous);
        $this->assertEquals($previous, $exception->getPrevious());
    }

    public function testExceptionIsInstanceOfException(): void
    {
        $exception = new ApiException('Test message');
        $this->assertInstanceOf(\Throwable::class, $exception);
    }
}