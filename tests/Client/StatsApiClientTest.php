<?php

namespace InterestAccountLibrary\Tests\Client;

use PHPUnit\Framework\TestCase;
use InterestAccountLibrary\Client\Services\StatsApiClient;
use InterestAccountLibrary\Exceptions\ApiException;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\TransferException;
use Mockery;
use Psr\Log\LoggerInterface;

class StatsApiClientTest extends TestCase
{
    private StatsApiClient $statsApiClient;
    private Client $httpClientMock;
    private MockHandler $mockHandler;
    private $loggerMock;

    protected function setUp(): void
    {
        $this->mockHandler = new MockHandler([]);
        $handlerStack = HandlerStack::create($this->mockHandler);
        $this->loggerMock = Mockery::mock(LoggerInterface::class);
        $this->loggerMock->shouldReceive('error')->andReturnNull();
        $this->httpClientMock = new Client(['handler' => $handlerStack]);
        $this->statsApiClient = new StatsApiClient($this->httpClientMock, 'https://stats.dev.chip.test/',  $this->loggerMock);
    }

    public function testCanRetrieveUserIncomeSuccessfully(): void
    {
        $userId = 'user-123';
        $this->mockHandler->append(new Response(200, [], json_encode(['id' => $userId, 'income' => 500000])));

        $income = $this->statsApiClient->getUserIncome($userId);

        $this->assertEquals(500000, $income);
    }

    public function testThrowsExceptionOnApiFailure(): void
    {
        $userId = 'user-123';
        $this->mockHandler->append(new RequestException("API Error", new Request('GET', "https://stats.dev.chip.test/users/$userId")));
    
        $this->loggerMock->shouldReceive('error')->once()->with(Mockery::type('string'))->andReturnNull();
    
        $this->expectException(ApiException::class);
        $this->statsApiClient->getUserIncome($userId);
    }

    public function testThrowsExceptionOnConnectionFailure(): void
    {
        $userId = 'user-123';
        $this->mockHandler->append(new ConnectException("Connection error", new Request('GET', "https://stats.dev.chip.test/users/$userId")));
    
        $this->loggerMock->shouldReceive('error')->once()->with(Mockery::type('string'))->andReturnNull();
    
        $this->expectException(ApiException::class);
        $this->statsApiClient->getUserIncome($userId);
    }

    public function testThrowsExceptionOnTransferFailure(): void
    {
        $userId = 'user-123';
        $this->mockHandler->append(new TransferException("Transfer error"));
    
        $this->loggerMock->shouldReceive('error')->once()->with(Mockery::type('string'))->andReturnNull();
    
        $this->expectException(ApiException::class);
        $this->statsApiClient->getUserIncome($userId);
    }

    public function testThrowsExceptionOnInvalidJsonResponse(): void
    {
        $userId = 'user-123';
        $this->mockHandler->append(new Response(200, [], "Invalid JSON Response"));
    
        $this->loggerMock->shouldReceive('error')->once()->with(Mockery::type('string'))->andReturnNull();
    
        $this->expectException(ApiException::class);
        $this->statsApiClient->getUserIncome($userId);
    }
}
