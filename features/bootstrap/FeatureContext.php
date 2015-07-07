<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Symfony\Component\Console\Input\ArrayInput;

class FeatureContext implements Context, SnippetAcceptingContext
{
    private $container;

    public function __construct()
    {
        $this->container = require __DIR__.'/app/container.php';
    }

    /**
     * @return \Dock\Cli\Application
     */
    private function getApplication()
    {
        return $this->container['application'];
    }

    /**
     * @Given I have a Docker Compose file that contains one container
     */
    public function iHaveADockerComposeFileThatContainsOneContainer()
    {

    }

    /**
     * @When I run the :command command
     */
    public function iRunTheCommand($command)
    {
        $this->getApplication()->run(new ArrayInput([$command]));
    }

    /**
     * @Then I should see the DNS resolution of the container
     */
    public function iShouldSeeTheDnsResolutionOfTheContainer()
    {

    }

    /**
     * @Given this container is not running
     */
    public function thisContainerIsNotRunning()
    {

    }

    /**
     * @Then I should see that this container has a status of :arg1
     */
    public function iShouldSeeThatThisContainerHasAStatusOf($arg1)
    {

    }

    /**
     * @Given this container is running
     */
    public function thisContainerIsRunning()
    {

    }
}
