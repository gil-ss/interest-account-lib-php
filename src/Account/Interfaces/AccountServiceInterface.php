<?php

namespace InterestAccountLibrary\Account\Interfaces;

interface AccountServiceInterface
{
    public function openAccount(string $userId): bool;
    public function getAccountStatement(string $userId): array;
}