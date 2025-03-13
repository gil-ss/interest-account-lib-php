<?php

namespace InterestAccountLibrary\Client\Interfaces;

interface StatsApiClientInterface
{
    public function getUserIncome(string $userId): ?int;
}
