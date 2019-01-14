<?php

namespace Inviqa\Zalando\Api\Client;

use GuzzleHttp\Client as GuzzleClient;
use Inviqa\Zalando\Api\Client;
use Inviqa\Zalando\Api\Configuration;
use InviqaTest\Zalando\MockClient;

class ClientFactory
{
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function createClient(): Client
    {
        return $this->configuration->isTestMode()
            ? new MockClient($this->configuration)
            : new ApiClient($this->configuration, new GuzzleClient());
    }
}
