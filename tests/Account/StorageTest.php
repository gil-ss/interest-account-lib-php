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
    
    protected function setUp(): void
    {
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
        $account = new InterestAccount('user-123', 0.01);
        $this->inMemoryStorage->save($account);
        
        $retrieved = $this->inMemoryStorage->get('user-123');
        
        $this->assertNotNull($retrieved);
        $this->assertEquals('user-123', $retrieved['userId']);
        $this->assertEquals(0.01, $retrieved['interestRate']);
    }

    public function testCanSaveAndRetrieveAccountJsonStorage(): void
    {
        $account = new InterestAccount('user-456', 0.02);
        $this->jsonStorage->save($account);
        
        $retrieved = $this->jsonStorage->get('user-456');
        
        $this->assertNotNull($retrieved);
        $this->assertEquals('user-456', $retrieved['userId']);
        $this->assertEquals(0.02, $retrieved['interestRate']);
    }

    public function testCanDeleteAccountInMemory(): void
    {
        $account = new InterestAccount('user-123', 0.01);
        $this->inMemoryStorage->save($account);
        $this->assertTrue($this->inMemoryStorage->delete('user-123'));
        $this->assertNull($this->inMemoryStorage->get('user-123'));
    }

    public function testCanDeleteAccountJsonStorage(): void
    {
        $account = new InterestAccount('user-456', 0.02);
        $this->jsonStorage->save($account);
        $this->assertTrue($this->jsonStorage->delete('user-456'));
        $this->assertNull($this->jsonStorage->get('user-456'));
    }
}
