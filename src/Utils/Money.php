<?php

namespace InterestAccountLibrary\Utils;

class Money implements MoneyInterface
{
    // Stores values ​​ALWAYS in cents [fixed-point representation]
    private int $amount;

    public function __construct(int $amount)
    {
        $this->amount = $amount;
    }

    public function add(MoneyInterface $amount): MoneyInterface
    {
        return new self($this->amount + $amount->getAmount());
    }

    public function subtract(MoneyInterface $amount): MoneyInterface
    {
        if ($amount->getAmount() > $this->amount) {
            throw new \InvalidArgumentException("Insufficient funds.");
        }
        return new self($this->amount - $amount->getAmount());
    }

    public function multiply(float $factor): MoneyInterface
    {
        return new self((int) round($this->amount * $factor));
    }

    public function toDecimal(): float
    {
        return $this->amount / 100;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }
}
