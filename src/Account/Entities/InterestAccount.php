<?php

namespace InterestAccountLibrary\Account\Entities;

use InterestAccountLibrary\Exceptions\AccountException;


class InterestAccount
{
    private string $userId;
    private float $balance;
    private float $interestRate;
    private array $transactions;

    public function __construct(string $userId, float $interestRate)
    {
        $this->userId = $userId;
        $this->balance = 0.0;
        $this->interestRate = $interestRate;
        $this->transactions = [];
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getBalance(): float
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

    public function deposit(float $amount): void
    {
        if ($amount <= 0) {
            throw new AccountException('Amount must be positive.');
        }

        $this->balance += $amount;
        $this->transactions[] = [
            'type' => 'deposit',
            'amount' => $amount,
            'date' => date('Y-m-d H:i:s'),
        ];
    }

    public function calculateInterest(): float
    {
        $interest = $this->balance * $this->interestRate;

        if ($interest >= 0.01) {
            $this->balance += $interest;
            $this->transactions[] = [
                'type' => 'interest',
                'amount' => $interest,
                'date' => date('Y-m-d H:i:s'),
            ];
            return $interest;
        }

        return 0.0;
    }

    public function toArray(): array
    {
        return [
            'userId' => $this->userId,
            'balance' => $this->balance,
            'transactions' => $this->transactions,
            'interestRate' => $this->interestRate,
        ];
    }

    public static function fromArray(array $data): InterestAccount
    {
        $account = new self($data['userId'], $data['interestRate']);
        $account->balance = $data['balance'];
        $account->transactions = $data['transactions'] ?? [];
        return $account;
    }
}