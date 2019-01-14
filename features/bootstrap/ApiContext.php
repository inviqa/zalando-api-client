<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Inviqa\Zalando\Api\Model\ArticlePrice;
use Inviqa\Zalando\Api\Model\AuthenticationData;
use Inviqa\Zalando\Api\Response\ArticlePriceUpdateResponse;
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
     * @var AuthenticationData|null
     */
    private $authenticationData;

    /**
     * @var ArticlePriceUpdateResponse|null
     */
    private $response;

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
     * @When I update the article price with the following details
     */
    public function iUpdateTheArticlePriceWithTheFollowingDetails(TableNode $table)
    {
        $data = $table->getRowsHash();
        $articlePrice = new ArticlePrice(
            $data['regular price'],
            $data['merchant simple ID'],
            $data['ean'],
            $data['sales channel ID']
        );

        $this->response = $this->application->updateArticlePrice($articlePrice);
    }

    /**
     * @Then a file with the following content will be written
     */
    public function aFileWithTheFollowingContentWillBeWritten(PyStringNode $expectedContent)
    {
        Assert::same($this->response->getRawRequest(), $expectedContent->getRaw());
    }
}
