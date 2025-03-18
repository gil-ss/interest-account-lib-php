<?php

namespace InterestAccountLibrary\Tests\Utils;

use PHPUnit\Framework\TestCase;
use InterestAccountLibrary\Utils\Money;
use InterestAccountLibrary\Utils\MoneyInterface;

class MoneyTest extends TestCase
{
    public function testCanInstantiateMoney(): void
    {
        $money = new Money(1000); // 10.00 in cents
        $this->assertInstanceOf(MoneyInterface::class, $money);
        $this->assertEquals(1000, $money->getAmount());
    }

    public function testAddMoney(): void
    {
        $money1 = new Money(1000);
        $money2 = new Money(500);
        
        $result = $money1->add($money2);
        
        $this->assertEquals(1500, $result->getAmount());
    }

    public function testSubtractMoney(): void
    {
        $money1 = new Money(1000);
        $money2 = new Money(300);
        
        $result = $money1->subtract($money2);
        
        $this->assertEquals(700, $result->getAmount());
    }

    public function testSubtractMoneyThrowsExceptionOnInsufficientFunds(): void
    {
        $money1 = new Money(500);
        $money2 = new Money(1000);
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Insufficient funds.");
        
        $money1->subtract($money2);
    }

    public function testMultiplyMoney(): void
    {
        $money = new Money(1000);
        
        $result = $money->multiply(1.5);
        
        $this->assertEquals(1500, $result->getAmount());
    }

    public function testToDecimalConversion(): void
    {
        $money = new Money(1234); // 12.34 in cents
        
        $this->assertEquals(12.34, $money->toDecimal());
    }
}
