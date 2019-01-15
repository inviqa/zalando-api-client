<?php

namespace Inviqa\Zalando\Api\Security;

use Exception;
use Inviqa\Zalando\Api\Client;
use Inviqa\Zalando\Api\Exception\ZalandoException;
use Psr\Log\LoggerInterface;

class Authenticator
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var AuthenticationStorage
     */
    private $storage;

    /**
     * @var null|LoggerInterface
     */
    private $logger;

    public function __construct(Client $client, AuthenticationStorage $storage, LoggerInterface $logger = null)
    {
        $this->client = $client;
        $this->storage = $storage;
        $this->logger = $logger;
    }

    public function authenticate(): AuthenticationParameters
    {
        $authenticationParameters = $this->getPreviousAuthenticationParameters();

        if (isset($authenticationParameters) && !$authenticationParameters->hasAccessTokenExpired()) {
            return $authenticationParameters;
        }

        try {
            $response = $this->client->authenticate();
        } catch (Exception $e) {
            throw new ZalandoException('Zalando authentication error: ' . $e->getMessage(), 0, $e);
        }

        $data = new AuthenticationParameters(array_merge(
            ['authenticated_at' => $response->getDate()],
            json_decode($response->getContent(), true)
        ));

        $this->storage->save($data);

        return $data;
    }

    private function getPreviousAuthenticationParameters(): ?AuthenticationParameters
    {
        try {
            return $this->storage->fetch();
        } catch (Exception $e) {
            $message = 'Fetching Zalando authentication parameters error: ' . $e->getMessage();

            if ($this->logger) {
                $this->logger->warning($message);
            } else {
                throw new ZalandoException($message);
            }
        }

        return null;
    }
}
