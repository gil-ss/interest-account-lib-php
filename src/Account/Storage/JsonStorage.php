<?php

namespace InterestAccountLibrary\Account\Storage;

use InterestAccountLibrary\Account\Interfaces\StorageInterface;
use InterestAccountLibrary\Exceptions\StorageException;
use InterestAccountLibrary\Account\Entities\InterestAccount;


class JsonStorage implements StorageInterface
{
    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        if (!file_exists($filePath)) {
            file_put_contents($filePath, json_encode([]));
        }
    }

    private function loadAccounts(): array
    {
        if (!file_exists($this->filePath)) {
            return [];
        }

        $content = file_get_contents($this->filePath);
        if ($content === false) {
            return [];
        }

        $accounts = json_decode($content, true);
        return $accounts ?? [];
    }

    public function save(InterestAccount $account): bool
    {
        $accounts = $this->loadAccounts();
        $accounts[$account->getUserId()] = $account->toArray();
        return $this->saveAccounts($accounts);
    }

    public function get(string $userId): ?InterestAccount
    {
        $accounts = $this->loadAccounts();
        return isset($accounts[$userId]) ? InterestAccount::fromArray($accounts[$userId]) : null;
    }

    public function update(InterestAccount $account): bool
    {
        $accounts = $this->loadAccounts();
        if (isset($accounts[$account->getUserId()])) {
            $accounts[$account->getUserId()] = $account->toArray();
            return $this->saveAccounts($accounts);
        }
        return false;
    }

    public function delete(string $userId): bool
    {
        $accounts = $this->loadAccounts();
        if (!isset($accounts[$userId])) {
            return false;
        }
        unset($accounts[$userId]);
        if (!$this->saveAccounts($accounts)) {
            throw new StorageException("Failed to delete account for user: $userId");
        }

        return true;
    }

    private function saveAccounts(array $accounts): bool
    {
        $content = json_encode($accounts, JSON_PRETTY_PRINT);
        if ($content === false) {
            return false;
        }

        $result = file_put_contents($this->filePath, $content);
        return $result !== false;
    }

    public function getAllAccounts(): array
    {
        return json_decode(file_get_contents($this->filePath), true) ?? [];
    }

}