<?php

namespace InterestAccountLibrary\Account\Services;

use InterestAccountLibrary\Account\Interfaces\AccountServiceInterface;
use InterestAccountLibrary\Account\Interfaces\StorageInterface;
use InterestAccountLibrary\Client\Interfaces\StatsApiClientInterface;
use InterestAccountLibrary\Exceptions\AccountException;
use InterestAccountLibrary\Account\Entities\InterestAccount;
use InterestAccountLibrary\Exceptions\ApiException;
use InterestAccountLibrary\Utils\Money;

class InterestAccountService implements AccountServiceInterface
{
    private StorageInterface $storage;
    private StatsApiClientInterface $statsApiClient;

    public function __construct(StorageInterface $storage, StatsApiClientInterface $statsApiClient)
    {
        $this->storage = $storage;
        $this->statsApiClient = $statsApiClient;
    }

    public function openAccount(string $userId): bool
    {
        if ($this->storage->get($userId)) {
            throw new AccountException("User already has an active interest account.");
        }

        try {
            $income = $this->statsApiClient->getUserIncome($userId);
            $interestRate = $this->determineInterestRate($income);
            
            $account = new InterestAccount($userId, $interestRate);
            return $this->storage->save($account);
        } catch (ApiException $e) {
            throw new AccountException("Failed to open account: " . $e->getMessage());
        }
    }

    public function getAccountStatement(string $userId): array
    {
        $account = $this->storage->get($userId);
        if (!$account) {
            throw new AccountException("User does not have an active account.");
        }

        return $account->getTransactions();
    }

    public function deposit(string $userId, Money $amount): void
    {
        $account = $this->storage->get($userId);
        if (!$account) {
            throw new AccountException("User does not have an active account.");
        }

        $account->deposit($amount);
        $this->storage->update($account);
    }

    public function calculateInterest(string $userId): bool
    {
        $account = $this->storage->get($userId);
        if (!$account) {
            throw new AccountException("User does not have an active account.");
        }

        $account->calculateInterest();
        return $this->storage->update($account);
    }

    private function determineInterestRate(?int $income): float
    {
        return match (true) {
            $income === null => 0.005,
            $income < 500000 => 0.0093,
            default => 0.0102,
        };
    }
}
