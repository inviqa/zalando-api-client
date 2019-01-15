<?php

namespace Inviqa\Zalando\Api\Response;

use DateTimeImmutable;
use Psr\Http\Message\ResponseInterface;
use Teapot\StatusCode;

class ClientResponse
{
    /**
     * @var bool
     */
    private $successful;

    /**
     * @var DateTimeImmutable
     */
    private $date;

    /**
     * @var string
     */
    private $content;

    public function __construct(ResponseInterface $response)
    {
        $this->successful = StatusCode::OK === $response->getStatusCode();
        $this->date = new DateTimeImmutable($response->getHeaderLine('Date') ?: '@' . time());
        $this->content = $response->getBody()->getContents();
    }

    public function isSuccessful(): bool
    {
        return $this->successful;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
