<?php

namespace InterestAccountLibrary\Account\Entities;

use InterestAccountLibrary\Exceptions\AccountException;
use InterestAccountLibrary\Utils\Money;

class InterestAccount
{
    private string $userId;
    private Money $balance;
    private float $interestRate;
    private array $transactions;

    public function __construct(string $userId, float $interestRate)
    {
        $this->userId = $userId;
        $this->balance = new Money(0);
        $this->interestRate = $interestRate;
        $this->transactions = [];
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getBalance(): Money
    {
        return $this->balance;
    }

    public function getTransactions(): array
    {
        return $this->transactions;
    }

    public function getInterestRate(): float
    {
        return $this->interestRate;
    }

    public function deposit(Money $amount): void
    {
        if ($amount <= 0) {
            throw new AccountException('Amount must be positive.');
        }

        $this->balance = $this->balance->add($amount);
        $this->transactions[] = [
            'type' => 'deposit',
            'amount' => $amount,
            'date' => date('Y-m-d H:i:s'),
        ];
    }

    public function calculateInterest(): Money
    {
        $interest = $this->balance->multiply($this->interestRate);

        // Only add if at least 1 cent
        if ($interest >= 1) {
            $this->balance = $this->balance->add($interest);
            $this->transactions[] = [
                'type' => 'interest',
                'amount' => $interest,
                'date' => date('Y-m-d H:i:s'),
            ];
            return $interest;
        }

        return new Money(0);
    }

    public function toArray(): array
    {
        return [
            'userId' => $this->userId,
            'balance' => $this->balance->getAmount(),
            'interestRate' => $this->interestRate,
            'transactions' => $this->transactions
        ];
    }

    public static function fromArray(array $data): InterestAccount
    {
        $account = new self($data['userId'], $data['interestRate']);
        $account->balance = new Money((int) $data['balance']);
        $account->transactions = $data['transactions'] ?? [];

        return $account;
    }
}
