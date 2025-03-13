<?php

namespace InterestAccountLibrary\Tests\Account;

use PHPUnit\Framework\TestCase;
use InterestAccountLibrary\Account\Entities\InterestAccount;

class InterestAccountTest extends TestCase
{
    private InterestAccount $account;

    protected function setUp(): void
    {
        $this->account = new InterestAccount('user-123', 0.01);
    }

    public function testInitialBalanceIsZero(): void
    {
        $this->assertEquals(0.0, $this->account->getBalance());
    }

    public function testCanCreateAccountWithInitialValues(): void
    {
        $this->assertEquals('user-123', $this->account->getUserId());
        $this->assertEquals(0.0, $this->account->getBalance());
        $this->assertEmpty($this->account->getTransactions());
    }

    public function testDepositIncreasesBalance(): void
    {
        $this->account->deposit(100.0);

        $this->assertEquals(100.0, $this->account->getBalance());
        $this->assertCount(1, $this->account->getTransactions());
    }

    public function testCalculateInterestAddsCorrectAmount(): void
    {
        $this->account->deposit(100.0);
        $this->account->calculateInterest();

        $expectedBalance = 100.0 + (100.0 * 0.01);
        $this->assertEquals($expectedBalance, $this->account->getBalance());
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $this->account->deposit(100.0);

        $array = $this->account->toArray();

        $this->assertEquals('user-123', $array['userId']);
        $this->assertEquals(100.0, $array['balance']);
        $this->assertEquals(0.01, $array['interestRate']);
        $this->assertCount(1, $array['transactions']);
    }

    public function testFromArrayReturnsCorrectStructure(): void
    {
        $this->account->deposit(100.0);
        $array = $this->account->toArray();
        $newAccount = InterestAccount::fromArray($array);
        $this->assertEquals($this->account->getUserId(), $newAccount->getUserId());
        $this->assertEquals($this->account->getBalance(), $newAccount->getBalance());
        $this->assertEquals($this->account->getInterestRate(), $newAccount->getInterestRate());
        $this->assertEquals($this->account->getTransactions(), $newAccount->getTransactions());
    }

    public function testFromArrayReconstructsAccount(): void
    {
        $data = [
            'userId' => 'user-123',
            'balance' => 200.0,
            'interestRate' => 0.01,
            'transactions' => [['type' => 'deposit', 'amount' => 200.0, 'date' => '2025-03-13']]
        ];

        $account = InterestAccount::fromArray($data);

        $this->assertEquals('user-123', $account->getUserId());
        $this->assertEquals(200.0, $account->getBalance());
        $this->assertCount(1, $account->getTransactions());
    }
}