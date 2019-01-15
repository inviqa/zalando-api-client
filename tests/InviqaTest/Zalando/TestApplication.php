<?php

namespace InviqaTest\Zalando;

use Inviqa\Zalando\Api\ZalandoConfiguration;
use Inviqa\Zalando\Application;
use Symfony\Component\Filesystem\Filesystem;

class TestApplication extends Application
{
    public function getClient(): AccountableClient
    {
        return $this->client;
    }

    public function getAuthenticationStorage(): TestAuthenticationStorage
    {
        return $this->authenticationStorage;
    }

    protected function createClient(ZalandoConfiguration $configuration): void
    {
        parent::createClient($configuration);
        $this->client = new AccountableClient($this->client);
    }

    protected function createAuthenticationStorage(string $filePath): void
    {
        $this->authenticationStorage = new TestAuthenticationStorage(new Filesystem(), $filePath);
    }
}
