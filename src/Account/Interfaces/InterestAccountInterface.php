<?php

namespace InterestAccountLibrary\Account\Interfaces;

interface InterestAccountInterface
{
    public function deposit(string $userId, float $amount): bool;
    public function calculateInterest(): void;
    public function getUserId(): string;
    public function getBalance(): float;
    public function getTransactions(): array;
}
