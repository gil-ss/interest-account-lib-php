<?php

namespace InterestAccountLibrary\Client\Services;

use InterestAccountLibrary\Client\Interfaces\StatsApiClientInterface;
use InterestAccountLibrary\Exceptions\ApiException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\TransferException;
use Psr\Log\LoggerInterface;

class StatsApiClient implements StatsApiClientInterface
{
    private string $baseUrl;
    private Client $httpClient;
    private LoggerInterface $logger;

    public function __construct(Client $httpClient, string $baseUrl, LoggerInterface $logger)
    {
        $this->httpClient = $httpClient;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->logger = $logger;
    }

    public function getUserIncome(string $userId): ?int
    {
        try {
            $response = $this->httpClient->get("{$this->baseUrl}/users/{$userId}", [
                'headers' => ['Accept' => 'application/json'],
                'timeout' => 5.0
            ]);

            if ($response->getStatusCode() !== 200) {
                $this->logger->error("Unexpected API response: " . $response->getStatusCode());
                throw new ApiException("Unexpected API response: " . $response->getStatusCode());
            }

            $body = (string) $response->getBody();
            $this->logger->error("API response body: " . $body);
            $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

            if (!isset($data['income'])) {
                $this->logger->error("API response does not contain 'income' field.");
                throw new ApiException("Invalid API response structure.");
            }

            return $data['income'];
        } catch (ConnectException $e) {
            $this->logger->error("Connection error: " . $e->getMessage());
            throw new ApiException("Failed to connect to Stats API.");
        } catch (RequestException $e) {
            $this->logger->error("Request error: " . $e->getMessage());
            throw new ApiException("Failed to fetch user income.");
        } catch (TransferException $e) {
            $this->logger->error("HTTP transfer error: " . $e->getMessage());
            throw new ApiException("An error occurred while communicating with the Stats API.");
        } catch (\JsonException $e) {
            $this->logger->error("JSON decode error: " . $e->getMessage());
            throw new ApiException("Failed to parse API response.");
        }
    }
}