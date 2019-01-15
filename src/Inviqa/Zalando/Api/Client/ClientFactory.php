<?php

namespace Inviqa\Zalando\Api\Client;

use GuzzleHttp\Client as GuzzleClient;
use Inviqa\Zalando\Api\Client;
use Inviqa\Zalando\Api\ZalandoConfiguration;
use InviqaTest\Zalando\MockClient;

class ClientFactory
{
    /**
     * @var ZalandoConfiguration
     */
    private $configuration;

    public function __construct(ZalandoConfiguration $configuration)
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
