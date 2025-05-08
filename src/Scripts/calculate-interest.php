#!/usr/bin/env php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use InterestAccountLibrary\Account\Services\InterestAccountService;
use InterestAccountLibrary\Account\Storage\JsonStorage;
use InterestAccountLibrary\Client\Services\StatsApiClient;
use GuzzleHttp\Client;
use Psr\Log\NullLogger;

try {
    // Initialize dependencies
    $storage = new JsonStorage('accounts.json');
    $httpClient = new Client();
    $statsApiClient = new StatsApiClient($httpClient, 'https://stats.dev.test/', new NullLogger());
    $service = new InterestAccountService($storage, $statsApiClient);

    // Retrieve all stored accounts
    $accounts = $storage->getAllAccounts();

    foreach ($accounts as $userId => $accountData) {
        echo "Calculating interest for User: $userId\n";
        try {
            $service->calculateInterest($userId);
            echo "✔ Interest calculated successfully.\n";
        } catch (Exception $e) {
            echo "Failed to calculate interest: " . $e->getMessage() . "\n";
        }
    }

    echo "Interest calculation process completed.\n";
} catch (\Exception $e) {
    error_log("Error calculating interest: " . $e->getMessage());
    echo "Error calculating interest. See log for more details.\n";
}