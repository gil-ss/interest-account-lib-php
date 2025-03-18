<?php

namespace InterestAccountLibrary\Account\Storage;

use InterestAccountLibrary\Account\Interfaces\StorageInterface;
use InterestAccountLibrary\Account\Entities\InterestAccount;

class InMemoryStorage implements StorageInterface
{
    private array $accounts = [];

    public function save(InterestAccount $account): bool
    {
        $this->accounts[$account->getUserId()] = $account->toArray();
        return true;
    }

    public function get(string $userId): ?InterestAccount
    {
        return isset($this->accounts[$userId]) ? InterestAccount::fromArray($this->accounts[$userId]) : null;
    }

    public function delete(string $userId): bool
    {
        if (isset($this->accounts[$userId])) {
            unset($this->accounts[$userId]);
            return true;
        }
        return false;
    }

    public function update(InterestAccount $account): bool
    {
        if (isset($this->accounts[$account->getUserId()])) {
            $this->accounts[$account->getUserId()] = $account->toArray();
            return true;
        }
        return false;
    }
}