<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Dock\Containers\Container;

class ApplicationTesterContext implements Context, SnippetAcceptingContext
{
    private $container;

    const CONTAINER_ID = '12541255';
    const SECOND_CONTAINER_ID = '8798757656';
    const FAKE_DNS = 'docker.hostname';

    public function __construct()
    {
        $this->container = require __DIR__ . '/app/container.php';
    }

    /**
     * @Given I have a Docker Compose file that contains one container
     */
    public function iHaveADockerComposeFileThatContainsOneContainer()
    {
        $this->container['containers.configured_container_ids']->setIds([self::CONTAINER_ID]);
    }

    /**
     * @Given this container is running
     */
    public function thisContainerIsRunning()
    {
        $this->container['containers.container_details']->setState(
            self::CONTAINER_ID,
            Container::STATE_RUNNING,
            self::FAKE_DNS
        );

        $this->container['logs']->setRunningContainerIds([self::CONTAINER_ID]);
    }

    /**
     * @Given this container ran previously but is not currently running
     */
    public function thisContainerIsNotRunning()
    {
        $this->container['containers.container_details']->setState(
            self::CONTAINER_ID,
            Container::STATE_EXITED,
            self::FAKE_DNS
        );
    }

    /**
     * @When I run the :command command
     */
    public function iRunTheCommand($command)
    {
        $this->container['application_tester']->run([$command]);
    }

    /**
     * @Then I should see that this container has a status of :status
     */
    public function iShouldSeeThatThisContainerHasAStatusOf($status)
    {
        if (!preg_match(
            '/CONTAINER_' . self::CONTAINER_ID . '.*' . preg_quote($status) . '/',
            $this->getApplicationOutput()
        )
        ) {
            throw new \Exception("Container status was not $status");
        }
    }

    /**
     * @Then I should see the DNS resolution of the container
     */
    public function iShouldSeeTheDnsResolutionOfTheContainer()
    {
        if (!preg_match(
            '/CONTAINER_' . self::CONTAINER_ID . '.*' . preg_quote(self::FAKE_DNS) . '/',
            $this->getApplicationOutput()
        )
        ) {
            throw new \Exception("Container DNS was not displayed as " . self::FAKE_DNS);
        }
    }

    /**
     * @return string
     */
    private function getApplicationOutput()
    {
        return $this->container['application_tester']->getDisplay();
    }

    /**
     * @Then I should see this container's logs
     */
    public function iShouldSeeThatThisContainerSLogs()
    {
        $id = self::CONTAINER_ID;

        if ($this->getApplicationOutput() !== "[$id] is running\n") {
            throw new \Exception('Logs for all components not displayed');
        }
    }

    /**
     * @Given I have a Docker Compose file that contains two containers
     */
    public function iHaveADockerComposeFileThatContainsTwoContainers()
    {
        $this->container['containers.configured_container_ids']->setIds(
            [self::CONTAINER_ID, self::SECOND_CONTAINER_ID]
        );
    }

    /**
     * @Given those containers are running
     */
    public function thoseContainersAreRunning()
    {
        $this->container['containers.container_details']->setState(
            self::CONTAINER_ID,
            Container::STATE_RUNNING,
            self::FAKE_DNS
        );

        $this->container['containers.container_details']->setState(
            self::SECOND_CONTAINER_ID,
            Container::STATE_RUNNING,
            self::FAKE_DNS
        );

        $this->container['logs']->setRunningContainerIds([self::CONTAINER_ID, self::SECOND_CONTAINER_ID]);
    }

    /**
     * @Then I should see those container's logs
     */
    public function iShouldSeeThatThoseContainerSLogs()
    {
        $id = self::CONTAINER_ID;
        $id2 = self::SECOND_CONTAINER_ID;

        if ($this->getApplicationOutput() !== "[$id] is running\n[$id2] is running\n") {
            throw new \Exception('Logs for all components not displayed');
        }
    }

    /**
     * @When I run the :command command for one of the components
     */
    public function iRunTheCommandForOneOfTheComponents($command)
    {
        $this->container['application_tester']->run(['command' => $command, 'component' => self::CONTAINER_ID]);
    }
}
