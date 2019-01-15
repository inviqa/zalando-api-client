<?php

namespace InviqaTest\Zalando;

use Exception;
use Inviqa\Zalando\Api\Client;
use Inviqa\Zalando\Api\Response\ClientResponse;

class AccountableClient implements Client
{
    /**
     * @var Client
     */
    private $decoratedClient;

    /**
     * @var int[]
     */
    private $apiCallCount = [];

    public function __construct(Client $decoratedClient)
    {
        $this->decoratedClient = $decoratedClient;
    }

    public function authenticate(): ClientResponse
    {
        try {
            return $this->decoratedClient->authenticate();
        } catch (Exception $e) {
            throw $e;
        } finally {
            $this->incrementApiCallCount(__FUNCTION__);
        }
    }

    public function getApiCallCount(string $action): int
    {
        return $this->apiCallCount[$action] ?? 0;
    }

    public function resetApiCallCounts(): void
    {
        $this->apiCallCount = [];
    }

    public function setFailOnApiCall(TestZalandoConfiguration $configuration, string $action): void
    {
        $configuration->setSecret('');

        if ($this->decoratedClient instanceof MockClient) {
            $this->decoratedClient->setFailOnApiCall($action);
        }
    }

    private function incrementApiCallCount(string $action): void
    {
        if (!isset($this->apiCallCount[$action])) {
            $this->apiCallCount[$action] = 0;
        }

        $this->apiCallCount[$action]++;
    }
}
