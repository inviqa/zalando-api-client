<?php

namespace Inviqa\Zalando\Api\Security;

use DateInterval;
use DateTimeImmutable;
use InvalidArgumentException;

class AuthenticationParameters
{
    /**
     * @var null|DateTimeImmutable
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
        if (!$parameters['authenticated_at'] instanceof DateTimeImmutable) {
            throw new InvalidArgumentException('authenticated_at parameter is not a ' . DateTimeImmutable::class);
        }

        if (empty($parameters['access_token'])) {
            throw new InvalidArgumentException('Access token cannot be empty');
        }

        $this->accessToken = $parameters['access_token'];
        $this->expiresIn = $parameters['expires_in'] ?? 0;
        $this->authenticatedAt = $parameters['authenticated_at'];
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

    public function toArray(): array
    {
        return [
            'authenticated_at' => $this->authenticatedAt,
            'access_token' => $this->accessToken,
            'expires_in' => $this->expiresIn,
        ];
    }

    public function equals(AuthenticationParameters $parameters)
    {
        return $parameters == $this;
    }
}
