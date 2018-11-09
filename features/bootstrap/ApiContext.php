<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Inviqa\Zalando\Api\Article\ArticleReference;
use Inviqa\Zalando\Api\ArticlePrice\ArticlePrice;
use Inviqa\Zalando\Api\Merchant\MerchantOperationMetadata;
use Inviqa\Zalando\Api\Model\AuthenticationData;
use Inviqa\Zalando\Api\Request\ArticlePriceUpdateRequest;
use Inviqa\Zalando\Application;
use InviqaTest\Zalando\TestConfiguration;
use Symfony\Component\Yaml\Yaml;
use Webmozart\Assert\Assert;

class ApiContext implements Context
{
    /**
     * @var Application
     */
    private $application;

    /**
     * @var null|AuthenticationData
     */
    private $authenticationData;

    /**
     * @var null|ArticlePriceUpdateRequest
     */
    private $request;

    public function __construct(bool $testMode)
    {
        if ($testMode) {
            $configuration = new TestConfiguration();
        } else {
            $yamlConfig = Yaml::parseFile(__DIR__ . '/../../tests/config/integration.yml');
            $configuration = new TestConfiguration($testMode, $yamlConfig['parameters']);
        }

        $this->application = new Application($configuration);
    }

    /**
     * @When I authenticate
     */
    public function iAuthenticate()
    {
        $this->authenticationData = $this->application->authenticate();

        Assert::isInstanceOf($this->authenticationData, AuthenticationData::class);
    }

    /**
     * @Then I will receive a new bearer token
     */
    public function iWillReceiveANewBearerToken()
    {
        Assert::string($this->authenticationData->getAccessToken());
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
     * @Then the article price update request content should be
     */
    public function theArticlePriceUpdateRequestContentShouldBe(PyStringNode $expectedContent)
    {
        Assert::same($this->request->getRawRequest(), $expectedContent->getRaw());
    }
}
