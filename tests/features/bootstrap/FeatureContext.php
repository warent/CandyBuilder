<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Tester\Exception\PendingException;
require_once("CandyBuilder.php");

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    private $MyBuilder = "", $MyString = "", $MyArray = [];
    
    /**
     * @Given I have a CandyBuilder :arg1
     */
    public function iHaveACandybuilder($arg1)
    {
        $this->MyBuilder = new CandyBuilder($arg1);
    }

    /**
     * @Given I have a string :arg1
     */
    public function iHaveAString($arg1)
    {
        $this->MyString = $arg1;
    }

    /**
     * @Given I have an array argument:
     */
    public function iHaveAnArrayArgument(TableNode $table)
    {
        foreach ($table as $row) {
            array_push($this->MyArray, [$row["href"], $row["linkstr"]]);
        }
    }

    /**
     * @When I run ShortWrap
     */
    public function iRunShortwrap()
    {
        CandyBuilder::ShortWrap($this->MyString, $this->MyArray);
    }

    /**
     * @Then I should get :arg1
     */
    public function iShouldGet($arg1)
    {
        if ($arg1 != "<a href='#1'>link one</a><a href='#2'>link two</a>") {
            throw new Exception("Fail");
        }
    }

}
