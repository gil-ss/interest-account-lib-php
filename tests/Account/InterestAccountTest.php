<?php

namespace InterestAccountLibrary\Tests\Account;

use PHPUnit\Framework\TestCase;
use InterestAccountLibrary\Account\Entities\InterestAccount;
use InterestAccountLibrary\Utils\Money;

class InterestAccountTest extends TestCase
{
    private InterestAccount $account;
    private string $userId;

    protected function setUp(): void
    {
        $this->userId = 'user-123';
        $this->account = new InterestAccount($this->userId, 0.01);
    }

    public function testInitialBalanceIsZero(): void
    {
        $this->assertEquals(new Money(0), $this->account->getBalance());
    }

    public function testCanCreateAccountWithInitialValues(): void
    {
        $this->assertEquals($this->userId, $this->account->getUserId());
        $this->assertEquals(new Money(0), $this->account->getBalance());
        $this->assertEmpty($this->account->getTransactions());
    }

    public function testDepositIncreasesBalance(): void
    {
        $this->account->deposit(new Money(10000));

        $this->assertEquals(new Money(10000), $this->account->getBalance());
        $this->assertCount(1, $this->account->getTransactions());
    }

    public function testCalculateInterestAddsCorrectAmount(): void
    {
        $this->account->deposit(new Money(10000));
        $this->account->calculateInterest();

        $expectedBalance = new Money(10000 + (10000 * 0.01));
        $this->assertEquals($expectedBalance, $this->account->getBalance());
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $this->account->deposit(new Money(10000));

        $array = $this->account->toArray();

        $this->assertEquals($this->userId, $array['userId']);
        $this->assertEquals(10000, $array['balance']);
        $this->assertEquals(0.01, $array['interestRate']);
        $this->assertCount(1, $array['transactions']);
    }

    public function testFromArrayReturnsCorrectStructure(): void
    {
        $this->account->deposit(new Money(10000));
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
            'userId' => $this->userId,
            'balance' => 20000,
            'interestRate' => 0.01,
            'transactions' => [['type' => 'deposit', 'amount' => 20000, 'date' => '2025-03-13']]
        ];

        $account = InterestAccount::fromArray($data);

        $this->assertEquals($this->userId, $account->getUserId());
        $this->assertEquals(20000, $account->getBalance()->getAmount());
        $this->assertCount(1, $account->getTransactions());
    }
}