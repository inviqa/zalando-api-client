<?php

namespace Inviqa\Zalando;

use Inviqa\Zalando\Api\ArticlePriceUpdater;
use Inviqa\Zalando\Api\Client\ClientFactory;
use Inviqa\Zalando\Api\Configuration;
use Inviqa\Zalando\Api\Model\ArticlePrice;
use Inviqa\Zalando\Api\Model\AuthenticationData;
use Inviqa\Zalando\Api\Response\ArticlePriceUpdateResponse;
use Inviqa\Zalando\Api\Security\AuthenticationStorage;
use Inviqa\Zalando\Api\Security\Authenticator;
use Inviqa\Zalando\Api\Writer\JsonFileWriterFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

class Application
{
    /**
     * @var Authenticator
     */
    private $authenticator;

    /**
     * @var ArticlePriceUpdater
     */
    private $articlePriceUpdater;

    public function __construct(Configuration $configuration, LoggerInterface $logger = null)
    {
        $clientFactory = new ClientFactory($configuration);
        $client = $clientFactory->createClient();
        $authenticationStorage = new AuthenticationStorage(new Filesystem(), $configuration);
        $this->authenticator = new Authenticator($client, $authenticationStorage, $logger);
        $this->articlePriceUpdater = new ArticlePriceUpdater(JsonFileWriterFactory::create());
    }

    public function authenticate(): AuthenticationData
    {
        return $this->authenticator->authenticate();
    }

    public function updateArticlePrice(ArticlePrice $articlePrice): ArticlePriceUpdateResponse
    {
        return $this->articlePriceUpdater->updateArticlePrice($articlePrice);
    }
}
