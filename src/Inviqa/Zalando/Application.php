<?php

namespace Inviqa\Zalando;

use Inviqa\Zalando\Api\ArticlePrice\ArticlePrice;
use Inviqa\Zalando\Api\Client\ClientFactory;
use Inviqa\Zalando\Api\Configuration;
use Inviqa\Zalando\Api\Merchant\MerchantOperationMetadata;
use Inviqa\Zalando\Api\Model\AuthenticationData;
use Inviqa\Zalando\Api\Request\ArticlePriceUpdateRequest;
use Inviqa\Zalando\Api\Security\AuthenticationStorage;
use Inviqa\Zalando\Api\Security\Authenticator;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

class Application
{
    /**
     * @var Authenticator
     */
    private $authenticator;

    public function __construct(Configuration $configuration, LoggerInterface $logger = null)
    {
        $clientFactory = new ClientFactory($configuration);
        $client = $clientFactory->createClient();
        $authenticationStorage = new AuthenticationStorage(new Filesystem(), $configuration);
        $this->authenticator = new Authenticator($client, $authenticationStorage, $logger);
    }

    public function authenticate(): AuthenticationData
    {
        return $this->authenticator->authenticate();
    }

    public function createArticlePriceUpdateRequest(
        ArticlePrice $price,
        MerchantOperationMetadata $metadata
    ): ArticlePriceUpdateRequest {
        return new ArticlePriceUpdateRequest($price, $metadata);
    }
}
