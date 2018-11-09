<?php

namespace Inviqa\Zalando\Api\Security;

use Exception;
use Inviqa\Zalando\Api\Client;
use Inviqa\Zalando\Api\Exception\ZalandoException;
use Inviqa\Zalando\Api\Model\AuthenticationData;
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
     * @var LoggerInterface|null
     */
    private $logger;

    public function __construct(Client $client, AuthenticationStorage $storage, LoggerInterface $logger = null)
    {
        $this->client = $client;
        $this->storage = $storage;
        $this->logger = $logger;
    }

    public function authenticate(): AuthenticationData
    {
        $authenticationData = $this->getPreviousAuthenticationData();

        if (isset($authenticationData) && !$authenticationData->hasAccessTokenExpired()) {
            return $authenticationData;
        }

        try {
            $response = $this->client->authenticate();
        } catch (Exception $e) {
            throw new ZalandoException('Zalando authentication error: ' . $e->getMessage(), 0, $e);
        }

        $data = array_merge(['authenticated_at' => $response->getDate()], json_decode($response->getContent(), true));

        $this->storage->save($data);

        return new AuthenticationData($data);
    }

    private function getPreviousAuthenticationData(): ?AuthenticationData
    {
        try {
            $data = $this->storage->fetch();

            return empty($data) ? null : new AuthenticationData($data);
        } catch (Exception $e) {
            if ($this->logger) {
                $this->logger->warning($e->getMessage());
            } else {
                throw new ZalandoException($e->getMessage());
            }
        }

        return null;
    }
}
