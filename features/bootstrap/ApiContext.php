<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Inviqa\Zalando\Api\Article\ArticlePrice;
use Inviqa\Zalando\Api\Article\ArticleReference;
use Inviqa\Zalando\Api\Exception\ZalandoException;
use Inviqa\Zalando\Api\Merchant\MerchantOperationMetadata;
use Inviqa\Zalando\Api\Request\ArticlePriceUpdateRequest;
use Inviqa\Zalando\Api\Security\AuthenticationParameters;
use InviqaTest\Zalando\AccountableClient;
use InviqaTest\Zalando\TestApplication;
use InviqaTest\Zalando\TestAuthenticationStorage;
use InviqaTest\Zalando\TestZalandoConfiguration;
use Symfony\Component\Yaml\Yaml;
use Webmozart\Assert\Assert;

class ApiContext implements Context
{
    private const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * @var bool
     */
    private $testMode;

    /**
     * @var TestZalandoConfiguration
     */
    private $configuration;

    /**
     * @var TestApplication
     */
    private $application;

    /**
     * @var AccountableClient
     */
    private $client;

    /**
     * @var TestAuthenticationStorage
     */
    private $authenticationStorage;

    /**
     * @var null|AuthenticationParameters
     */
    private $previousAuthenticationParameters;

    /**
     * @var null|AuthenticationParameters
     */
    private $authenticationParameters;

    /**
     * @var null|ArticlePriceUpdateRequest
     */
    private $request;

    /**
     * @var null|Exception
     */
    private $exception;

    /**
     * @var null|DateTimeImmutable
     */
    private $startedAuthentication;

    public function __construct(bool $testMode)
    {
        $this->testMode = $testMode;

        if ($this->testMode) {
            $this->configuration = new TestZalandoConfiguration();
        } else {
            $yamlConfig = Yaml::parseFile(__DIR__ . '/../../tests/config/integration.yml');
            $this->configuration = new TestZalandoConfiguration($yamlConfig['parameters'], $this->testMode);
        }

        $this->application = new TestApplication($this->configuration);
        $this->client = $this->application->getClient();
        $this->authenticationStorage = $this->application->getAuthenticationStorage();
    }

    /**
     * @BeforeScenario
     */
    public function initialize()
    {
        $this->previousAuthenticationParameters = null;
        $this->authenticationParameters = null;
        $this->request = null;
        $this->exception = null;
        $this->startedAuthentication = null;
    }

    /**
     * @Given the Zalando API will fail to :action
     */
    public function theZalandoApiWillFailTo(string $action)
    {
        $this->client->setFailOnApiCall($this->configuration, $action);
    }

    /**
     * @Given an authentication parameters file does not exist
     */
    public function anAuthenticationParametersFileDoesNotExist()
    {
        $this->authenticationStorage->delete();

        Assert::false($this->authenticationStorage->fileExists());
    }

    /**
     * @Given there is an authentication parameters file containing:
     */
    public function thereIsAnAuthenticationParametersFileContaining(TableNode $table)
    {
        $this->authenticationStorage->save(
            new AuthenticationParameters($this->formatAuthenticationParametersArray($table))
        );
        $this->previousAuthenticationParameters = $this->authenticationStorage->fetch();

        Assert::isInstanceOf($this->previousAuthenticationParameters, AuthenticationParameters::class);
    }

    /**
     * @When I authenticate
     */
    public function iAuthenticate()
    {
        try {
            $this->startedAuthentication = new DateTime('@' . time());
            $this->authenticationParameters = $this->application->authenticate();
        } catch (ZalandoException $e) {
            $this->exception = $e;
        }
    }

    /**
     * @When I create an article price update request with the following details
     */
    public function iCreateAnArticlePriceUpdateRequestWithTheFollowingDetails(TableNode $table)
    {
        $data = $table->getRowsHash();
        $price = new ArticlePrice(
            new ArticleReference($data['merchant simple ID'], $data['ean']),
            $data['regular price'],
            $data['currency'],
            $data['VAT code']
        );
        $fulfilledBy = 'fulfilled_by_' . strtolower($data['fulfilled by']);
        $metadata = new MerchantOperationMetadata($data['sales channel ID'], $fulfilledBy);

        $this->request = $this->application->createArticlePriceUpdateRequest($price, $metadata);
    }

    /**
     * @Then authentication parameters were not returned
     */
    public function authenticationParametersWereNotReturned()
    {
        Assert::null($this->authenticationParameters);
    }

    /**
     * @Then I received an error response containing the message :message
     */
    public function iReceivedAnErrorResponseContainingTheMessage(string $message)
    {
        $exceptionMessage = $this->exception->getMessage();

        Assert::startsWith($exceptionMessage, 'Zalando authentication error: ');
        Assert::contains($exceptionMessage, $message);
    }

    /**
     * @Then the authentication parameters returned were authenticated most recently
     */
    public function theAuthenticationParametersReturnedWereAuthenticatedMostRecently()
    {
        $authenticatedAt = $this->authenticationParameters->getAuthenticatedAt();

        Assert::greaterThanEq($authenticatedAt, $this->startedAuthentication, sprintf(
            '%s was not authenticated after %s',
            $authenticatedAt->format(self::DATE_TIME_FORMAT),
            $this->startedAuthentication->format(self::DATE_TIME_FORMAT)
        ));
    }

    /**
     * @Then the authentication parameters returned matches the previous authentication parameters
     */
    public function theAuthenticationParametersReturnedMatchesThePreviousAuthenticationParameters()
    {
        Assert::isInstanceOf($this->previousAuthenticationParameters, AuthenticationParameters::class);
        Assert::isInstanceOf($this->authenticationParameters, AuthenticationParameters::class);
        Assert::null($this->exception, 'Caught an exception');
        Assert::false(
            $this->previousAuthenticationParameters->hasAccessTokenExpired(),
            'The previous authentication parameters access token should not have expired'
        );
        Assert::true(
            $this->authenticationParameters->equals($this->previousAuthenticationParameters),
            'The authentication parameters does not match those from when I had first authenticated'
        );
    }

    /**
     * @Then the authentication parameters returned does not match the previous authentication parameters
     */
    public function theAuthenticationParametersReturnedDoesNotMatchThePreviousAuthenticationParameters()
    {
        Assert::isInstanceOf($this->previousAuthenticationParameters, AuthenticationParameters::class);
        Assert::isInstanceOf($this->authenticationParameters, AuthenticationParameters::class);
        Assert::null($this->exception, 'Caught an exception');
        Assert::true(
            $this->previousAuthenticationParameters->hasAccessTokenExpired(),
            'The previous authentication parameters access token should have expired'
        );
        Assert::false(
            $this->authenticationParameters->equals($this->previousAuthenticationParameters),
            'The authentication parameters matches those from when I had first authenticated'
        );
    }

    /**
     * @Then the access token has not expired
     */
    public function theAccessTokenHasNotExpired()
    {
        Assert::false($this->authenticationParameters->hasAccessTokenExpired());
    }

    /**
     * @Then the access token expires in :expiresIn seconds
     */
    public function theAccessTokenExpiresInSeconds(int $expiresIn)
    {
        $expiry = $this->authenticationParameters->getExpiry();
        $expectedExpiry = $this->authenticationParameters->getAuthenticatedAt()->modify('+ ' . $expiresIn . ' second');

        Assert::same($expiresIn, $this->authenticationParameters->getExpiresIn());
        Assert::eq($expiry, $expectedExpiry, sprintf(
            'Expected to expire at %s, but got %s',
            $expectedExpiry->format(self::DATE_TIME_FORMAT),
            $expiry->format(self::DATE_TIME_FORMAT)
        ));
    }

    /**
     * @Then a Zalando API call to :action was made
     */
    public function aZalandoApiCallToActionWasMade(string $action)
    {
        Assert::same($this->client->getApiCallCount($action), 1);
    }

    /**
     * @Then a Zalando API call to :action was not made
     */
    public function aZalandoApiCallToActionWasNotMade(string $action)
    {
        Assert::same($this->client->getApiCallCount($action), 0);
    }

    /**
     * @Then the access token is :accessToken
     */
    public function theAccessTokenIs(string $accessToken)
    {
        Assert::same($this->authenticationParameters->getAccessToken(), $accessToken);
    }

    /**
     * @Then the article price update request content should be
     */
    public function theArticlePriceUpdateRequestContentShouldBe(PyStringNode $expectedContent)
    {
        Assert::same($this->request->getRawRequest(), $expectedContent->getRaw());
    }

    private function formatAuthenticationParametersArray(TableNode $table): array
    {
        $parameters = $table->getRowsHash();
        $timestamp = (new DateTime($parameters['authenticated_at']))->getTimestamp();
        $parameters['authenticated_at'] = new DateTimeImmutable('@' . $timestamp) ?? null;
        $parameters['expires_in'] = (int) $parameters['expires_in'];

        return $parameters;
    }
}
