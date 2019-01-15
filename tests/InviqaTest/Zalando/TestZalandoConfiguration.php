<?php

namespace InviqaTest\Zalando;

use Inviqa\Zalando\Api\ZalandoConfiguration;

class TestZalandoConfiguration implements ZalandoConfiguration
{
    private const AUTHENTICATION_PARAMETERS_FILE_PATH = __DIR__ . '/../../config/%sauthentication/authentication.yml';

    /**
     * @var bool
     */
    private $testMode;

    /**
     * @var string
     */
    private $merchantId;

    /**
     * @var string
     */
    private $authenticationEndpointUrl;

    /**
     * @var string
     */
    private $articlePriceUpdateEndpointUrl;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $secret;

    public function __construct(array $parameters = [], bool $testMode = true)
    {
        $this->testMode = $testMode;
        $this->merchantId = $parameters['merchant_id'] ?? '';
        $this->authenticationEndpointUrl = $parameters['authentication_endpoint_url'] ?? '';
        $this->articlePriceUpdateEndpointUrl = $parameters['article_price_update_endpoint_url'] ?? '';
        $this->username = $parameters['username'] ?? '';
        $this->secret = $parameters['secret'] ?? '';
    }

    public function isTestMode(): bool
    {
        return $this->testMode;
    }

    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    public function getAuthenticationParametersFilePath(): string
    {
        return sprintf(self::AUTHENTICATION_PARAMETERS_FILE_PATH, $this->testMode ? 'test_' : '');
    }

    public function getAuthenticationEndpointUrl(): string
    {
        return $this->authenticationEndpointUrl;
    }

    public function getArticlePriceUpdateEndpointUrl(): string
    {
        return sprintf($this->articlePriceUpdateEndpointUrl, $this->merchantId);
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getSecret(): string
    {
        return $this->secret;
    }

    public function setSecret(string $secret): void
    {
        $this->secret = $secret;
    }
}
