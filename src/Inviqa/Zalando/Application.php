<?php

namespace Inviqa\Zalando;

use Inviqa\Zalando\Api\Article\ArticlePrice;
use Inviqa\Zalando\Api\Client;
use Inviqa\Zalando\Api\Client\ClientFactory;
use Inviqa\Zalando\Api\Merchant\MerchantOperationMetadata;
use Inviqa\Zalando\Api\Request\ArticlePriceUpdateRequest;
use Inviqa\Zalando\Api\Security\AuthenticationParameters;
use Inviqa\Zalando\Api\Security\AuthenticationStorage;
use Inviqa\Zalando\Api\Security\Authenticator;
use Inviqa\Zalando\Api\ZalandoConfiguration;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

class Application
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var AuthenticationStorage
     */
    protected $authenticationStorage;

    /**
     * @var Authenticator
     */
    private $authenticator;

    /**
     * @var null|LoggerInterface
     */
    private $logger;

    public function __construct(ZalandoConfiguration $configuration, LoggerInterface $logger = null)
    {
        $this->createClient($configuration);
        $this->createAuthenticationStorage($configuration->getAuthenticationParametersFilePath());
        $this->authenticator = new Authenticator($this->client, $this->authenticationStorage, $logger);
        $this->logger = $logger;
    }

    public function getLogger(): ?LoggerInterface
    {
        return $this->logger;
    }

    public function authenticate(): AuthenticationParameters
    {
        return $this->authenticator->authenticate();
    }

    public function createArticlePriceUpdateRequest(
        ArticlePrice $price,
        MerchantOperationMetadata $metadata
    ): ArticlePriceUpdateRequest {
        return new ArticlePriceUpdateRequest($price, $metadata);
    }

    protected function createClient(ZalandoConfiguration $configuration): void
    {
        $clientFactory = new ClientFactory($configuration);
        $this->client = $clientFactory->createClient();
    }

    protected function createAuthenticationStorage(string $filePath): void
    {
        $this->authenticationStorage = new AuthenticationStorage(new Filesystem(), $filePath);
    }
}
