<?php

namespace InterestAccountLibrary\Tests\Account;

use PHPUnit\Framework\TestCase;
use InterestAccountLibrary\Account\Services\InterestAccountService;
use InterestAccountLibrary\Account\Interfaces\StorageInterface;
use InterestAccountLibrary\Client\Interfaces\StatsApiClientInterface;
use InterestAccountLibrary\Exceptions\AccountException;
use InterestAccountLibrary\Account\Entities\InterestAccount;
use InterestAccountLibrary\Exceptions\ApiException;
use InterestAccountLibrary\Utils\Money;
use Mockery;

class InterestAccountServiceTest extends TestCase
{
    private InterestAccountService $service;
    private $storageMock;
    private $statsApiMock;
    private $userId;

    protected function setUp(): void
    {
        $this->storageMock = Mockery::mock(StorageInterface::class);
        $this->statsApiMock = Mockery::mock(StatsApiClientInterface::class);
        $this->service = new InterestAccountService($this->storageMock, $this->statsApiMock);
        $this->userId = 'user-123';
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testCanOpenAccountSuccessfully(): void
    {
        $income = 600000;

        $this->storageMock->shouldReceive('get')->with($this->userId)->andReturnNull();
        $this->statsApiMock->shouldReceive('getUserIncome')->with($this->userId)->andReturn($income);
        $this->storageMock->shouldReceive('save')->with(Mockery::type(InterestAccount::class))->andReturnTrue();

        $result = $this->service->openAccount($this->userId);

        $this->assertTrue($result);
    }

    public function testCannotOpenDuplicateAccount(): void
    {
        $mockAccount = new InterestAccount($this->userId, 0.01);
        $this->storageMock->shouldReceive('get')->with($this->userId)->andReturn($mockAccount);

        $this->expectException(AccountException::class);
        $this->service->openAccount($this->userId);
    }

    public function testDepositToExistingAccount(): void
    {
        $mockAccount = new InterestAccount($this->userId, 0.01);
        $this->storageMock->shouldReceive('get')->with($this->userId)->andReturn($mockAccount);


        $this->storageMock->shouldReceive('get')->with($this->userId)->andReturn($mockAccount);
        $this->storageMock->shouldReceive('update')->with(Mockery::type(InterestAccount::class))->andReturnTrue();
  
        $this->service->deposit($this->userId, new Money(5000));

        $this->assertTrue(true);
    }

    public function testDepositToNonExistentAccountThrowsException(): void
    {
        $this->storageMock->shouldReceive('get')->with($this->userId)->andReturnNull();

        $this->expectException(AccountException::class);
        $this->service->deposit($this->userId, new Money(5000));
    }

    public function testCalculateInterestForExistingAccount(): void
    {
        $mockAccount = new InterestAccount($this->userId, 0.01);
        $mockAccount->deposit(new Money(10000));

        $this->storageMock->shouldReceive('get')->with($this->userId)->andReturn($mockAccount);
        $this->storageMock->shouldReceive('update')->with(Mockery::type(InterestAccount::class))->andReturnTrue();


        $this->storageMock->shouldReceive('get')->with($this->userId)->andReturn($mockAccount);
        $this->storageMock->shouldReceive('save')->with(Mockery::type(InterestAccount::class))->andReturnTrue();

        $result = $this->service->calculateInterest($this->userId);

        $this->assertTrue($result);
    }

    public function testGetAccountStatement(): void
    {
        $mockAccount = new InterestAccount($this->userId, 0.01);
        $mockAccount->deposit(new Money(10000));

        $this->storageMock->shouldReceive('get')->with($this->userId)->andReturn($mockAccount);

        $statement = $this->service->getAccountStatement($this->userId);
        $this->assertIsArray($statement);
        $this->assertCount(1, $statement);
        $this->assertEquals('deposit', $statement[0]['type']);
    }

    public function testOpenAccountFailsOnApiFailure(): void
    {
        $this->storageMock->shouldReceive('get')->with($this->userId)->andReturnNull();
        $this->statsApiMock->shouldReceive('getUserIncome')->with($this->userId)->andThrow(new ApiException("API Error"));

        $this->expectException(AccountException::class);
        $this->service->openAccount($this->userId);
    }
}