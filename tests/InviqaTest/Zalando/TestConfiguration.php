<?php

namespace InviqaTest\Zalando;

use Inviqa\Zalando\Api\Configuration;

class TestConfiguration implements Configuration
{
    private const DEFAULT_AUTHENTICATION_CONFIG_FILE_PATH = __DIR__ . '/../../../app/config/zalando_authentication.yml';

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
    private $authenticationConfigFilePath;

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

    public function __construct(bool $testMode = true, array $parameters = [])
    {
        $this->testMode = $testMode;
        $this->merchantId = $parameters['merchant_id'] ?? '';
        $this->authenticationConfigFilePath = $parameters['authentication_config_file_path']
            ?? self::DEFAULT_AUTHENTICATION_CONFIG_FILE_PATH;
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

    public function getAuthenticationConfigFilePath(): string
    {
        return $this->authenticationConfigFilePath;
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
}
