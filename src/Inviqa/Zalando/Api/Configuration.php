<?php

namespace Inviqa\Zalando\Api;

use SplFileObject;

interface Configuration
{
    public function isTestMode(): bool;

    public function getMerchantId(): string;

    public function getAuthenticationConfigFilePath(): string;

    public function getAuthenticationEndpointUrl(): string;

    public function getArticlePriceUpdateEndpointUrl(): string;

    public function getUsername(): string;

    public function getSecret(): string;
}
