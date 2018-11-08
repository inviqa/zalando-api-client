<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Inviqa\Zalando\Api\Model\ArticlePrice;
use Inviqa\Zalando\Api\Response\ArticlePriceUpdateResponse;
use Inviqa\Zalando\Application;
use Webmozart\Assert\Assert;

class ApiContext implements Context
{
    /**
     * @var Application
     */
    private $application;

    /**
     * @var ArticlePriceUpdateResponse|null
     */
    private $response;

    public function __construct()
    {
        $this->application = new Application();
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
