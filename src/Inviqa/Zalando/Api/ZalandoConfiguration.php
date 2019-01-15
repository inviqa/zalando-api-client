<?php

namespace Inviqa\Zalando\Api;

interface ZalandoConfiguration
{
    public function isTestMode(): bool;

    public function getMerchantId(): string;

    public function getAuthenticationParametersFilePath(): string;

    public function getAuthenticationEndpointUrl(): string;

    public function getArticlePriceUpdateEndpointUrl(): string;

    public function getUsername(): string;

    public function getSecret(): string;
}
