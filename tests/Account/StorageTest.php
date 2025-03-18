<?php

namespace InterestAccountLibrary\Tests\Account;

use PHPUnit\Framework\TestCase;
use InterestAccountLibrary\Account\Storage\InMemoryStorage;
use InterestAccountLibrary\Account\Storage\JsonStorage;
use InterestAccountLibrary\Account\Entities\InterestAccount;

class StorageTest extends TestCase
{
    private InMemoryStorage $inMemoryStorage;
    private JsonStorage $jsonStorage;
    private string $testFile;
    private string $userId;
    
    protected function setUp(): void
    {
        $this->userId = 'user-123';
        $this->inMemoryStorage = new InMemoryStorage();
        
        // Setup temporary JSON storage file for testing
        $this->testFile = __DIR__ . '/test_storage.json';
        file_put_contents($this->testFile, json_encode([]));
        $this->jsonStorage = new JsonStorage($this->testFile);
    }

    protected function tearDown(): void
    {
        // Remove temporary file after tests
        if (file_exists($this->testFile)) {
            unlink($this->testFile);
        }
    }

    public function testCanSaveAndRetrieveAccountInMemory(): void
    {
        $account = new InterestAccount($this->userId, 0.01);
        $this->inMemoryStorage->save($account);
        
        $retrieved = $this->inMemoryStorage->get($this->userId);
        
        $this->assertNotNull($retrieved);
        $this->assertEquals($this->userId, $retrieved->getUserId());
        $this->assertEquals(0.01, $retrieved->getInterestRate());
    }

    public function testCanSaveAndRetrieveAccountJsonStorage(): void
    {
        $account = new InterestAccount($this->userId, 0.02);
        $this->jsonStorage->save($account);
        
        $retrieved = $this->jsonStorage->get($this->userId);
        
        $this->assertNotNull($retrieved);
        $this->assertEquals($this->userId, $retrieved->getUserId());
        $this->assertEquals(0.02, $retrieved->getInterestRate());
    }

    public function testCanDeleteAccountInMemory(): void
    {
        $account = new InterestAccount($this->userId, 0.01);
        $this->inMemoryStorage->save($account);
        $this->assertTrue($this->inMemoryStorage->delete($this->userId));
        $this->assertNull($this->inMemoryStorage->get($this->userId));
    }

    public function testCanDeleteAccountJsonStorage(): void
    {
        $account = new InterestAccount($this->userId, 0.02);
        $this->jsonStorage->save($account);
        $this->assertTrue($this->jsonStorage->delete($this->userId));
        $this->assertNull($this->jsonStorage->get($this->userId));
    }
}
