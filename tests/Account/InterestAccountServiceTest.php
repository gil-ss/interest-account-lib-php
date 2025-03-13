<?php

namespace InterestAccountLibrary\Tests\Account;

use PHPUnit\Framework\TestCase;
use InterestAccountLibrary\Account\Services\InterestAccountService;
use InterestAccountLibrary\Account\Interfaces\StorageInterface;
use InterestAccountLibrary\Client\Interfaces\StatsApiClientInterface;
use InterestAccountLibrary\Exceptions\AccountException;
use InterestAccountLibrary\Account\Entities\InterestAccount;
use InterestAccountLibrary\Exceptions\ApiException;
use Mockery;

class InterestAccountServiceTest extends TestCase
{
    private InterestAccountService $service;
    private $storageMock;
    private $statsApiMock;

    protected function setUp(): void
    {
        $this->storageMock = Mockery::mock(StorageInterface::class);
        $this->statsApiMock = Mockery::mock(StatsApiClientInterface::class);
        $this->service = new InterestAccountService($this->storageMock, $this->statsApiMock);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testCanOpenAccountSuccessfully(): void
    {
        $userId = 'user-123';
        $income = 600000;

        $this->storageMock->shouldReceive('get')->with($userId)->andReturnNull();
        $this->statsApiMock->shouldReceive('getUserIncome')->with($userId)->andReturn($income);
        $this->storageMock->shouldReceive('save')->with(Mockery::type(InterestAccount::class))->andReturnTrue();

        $result = $this->service->openAccount($userId);

        $this->assertTrue($result);
    }

    public function testCannotOpenDuplicateAccount(): void
    {
        $userId = 'user-123';
        $this->storageMock->shouldReceive('get')->with($userId)->andReturn(['userId' => $userId]);

        $this->expectException(AccountException::class);
        $this->service->openAccount($userId);
    }

    public function testDepositToExistingAccount(): void
    {
        $userId = 'user-123';
        $accountData = ['userId' => $userId, 'balance' => 100, 'interestRate' => 0.01, 'transactions' => []];

        $this->storageMock->shouldReceive('get')->with($userId)->andReturn($accountData);
        $this->storageMock->shouldReceive('update')->with(Mockery::type(InterestAccount::class))->andReturnTrue();

        $this->service->deposit($userId, 50);

        $this->assertTrue(true);
    }

    public function testDepositToNonExistentAccountThrowsException(): void
    {
        $userId = 'user-123';
        $this->storageMock->shouldReceive('get')->with($userId)->andReturnNull();

        $this->expectException(AccountException::class);
        $this->service->deposit($userId, 50);
    }

    public function testCalculateInterestForExistingAccount(): void
    {
        $userId = 'user-123';
        $accountData = ['userId' => $userId, 'balance' => 100, 'interestRate' => 0.01, 'transactions' => []];

        $this->storageMock->shouldReceive('get')->with($userId)->andReturn($accountData);
        $this->storageMock->shouldReceive('save')->with(Mockery::type(InterestAccount::class))->andReturnTrue();

        $result = $this->service->calculateInterest($userId);

        $this->assertTrue($result);
    }

    public function testGetAccountStatement(): void
    {
        $userId = 'user-123';
        $accountData = [
            'userId' => $userId,
            'balance' => 100,
            'interestRate' => 0.01,
            'transactions' => [['type' => 'deposit', 'amount' => 100, 'date' => '2025-03-13']]
        ];

        $this->storageMock->shouldReceive('get')->with($userId)->andReturn($accountData);

        $statement = $this->service->getAccountStatement($userId);
        $this->assertIsArray($statement);
        $this->assertCount(1, $statement);
        $this->assertEquals('deposit', $statement[0]['type']);
    }

    public function testOpenAccountFailsOnApiFailure(): void
    {
        $userId = 'user-123';
        $this->storageMock->shouldReceive('get')->with($userId)->andReturnNull();
        $this->statsApiMock->shouldReceive('getUserIncome')->with($userId)->andThrow(new ApiException("API Error"));

        $this->expectException(AccountException::class);
        $this->service->openAccount($userId);
    }
}