<?php

namespace InterestAccountLibrary\Utils;

interface MoneyInterface
{
    public function add(MoneyInterface $amount): MoneyInterface;
    public function subtract(MoneyInterface $amount): MoneyInterface;
    public function multiply(float $factor): MoneyInterface;
    public function toDecimal(): float;
    public function getAmount(): int;
}
