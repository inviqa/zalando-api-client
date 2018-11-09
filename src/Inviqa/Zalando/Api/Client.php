<?php

namespace Inviqa\Zalando\Api;

use Inviqa\Zalando\Api\Response\ClientResponse;

interface Client
{
    public function authenticate(): ClientResponse;
}
