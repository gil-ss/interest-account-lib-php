<?php

namespace InterestAccountLibrary\Account\Interfaces;

use InterestAccountLibrary\Account\Entities\InterestAccount;

interface StorageInterface
{
    public function save(InterestAccount $account): bool;
    public function get(string $userId): ?InterestAccount;
    public function delete(string $userId): bool;
    public function update(InterestAccount $account): bool;
}