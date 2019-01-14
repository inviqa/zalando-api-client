<?php

namespace Inviqa\Zalando\Api\Model;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use InvalidArgumentException;

class AuthenticationData
{
    /**
     * @var DateTimeImmutable|null
     */
    private $authenticatedAt;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var int
     */
    private $expiresIn;

    public function __construct(array $parameters)
    {
        $this->accessToken = $parameters['access_token'] ?? '';
        $this->expiresIn = $parameters['expires_in'] ?? 0;

        if ($parameters['authenticated_at'] instanceof DateTime) {
            $this->authenticatedAt = DateTimeImmutable::createFromMutable($parameters['authenticated_at']);
        } else {
            $this->authenticatedAt = $parameters['authenticated_at'] ?? null;
        }

        $this->validate();
    }

    public function getAuthenticatedAt(): DateTimeImmutable
    {
        return $this->authenticatedAt;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getExpiresIn(): int
    {
        return $this->expiresIn;
    }

    public function getExpiry(): DateTimeImmutable
    {
        return $this->authenticatedAt->add(new DateInterval(sprintf('PT%uS', $this->expiresIn)));
    }

    public function hasAccessTokenExpired(): bool
    {
        return $this->getExpiry() < (new DateTimeImmutable('10 seconds'));
    }

    private function validate(): void
    {
        if (!$this->authenticatedAt instanceof DateTimeImmutable) {
            throw new InvalidArgumentException('authenticated_at parameter is not a ' . DateTimeImmutable::class);
        }

        if (empty($this->accessToken)) {
            throw new InvalidArgumentException('Access token cannot be empty');
        }
    }
}
