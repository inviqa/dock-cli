<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Dock\Docker\Containers\Container;
use Symfony\Component\Console\Output\OutputInterface;

class ApplicationTesterContext implements Context, SnippetAcceptingContext
{
    private $container;

    const CONTAINER_ID = '12541255';
    const SECOND_CONTAINER_ID = '8798757656';
    const FAKE_DNS = 'docker.hostname';

    public function __construct()
    {
        $this->container = new \Test\Container();
    }

    /**
     * @Given I have a Docker Compose file that contains one container
     */
    public function iHaveADockerComposeFileThatContainsOneContainer()
    {
        $this->container->getConfiguredContainerIds()->setIds([self::CONTAINER_ID]);
    }

    /**
     * @Given this container is running
     */
    public function thisContainerIsRunning()
    {
        $this->container->getContainerDetails()->setState(
            self::CONTAINER_ID,
            Container::STATE_RUNNING,
            self::FAKE_DNS
        );

        $this->container->getLogs()->setRunningContainerIds([self::CONTAINER_ID]);
    }

    /**
     * @Given this container is not running
     */
    public function thisContainerIsNotRunning()
    {
        $this->container->getContainerDetails()->setState(
            self::CONTAINER_ID,
            Container::STATE_EXITED,
            self::FAKE_DNS
        );

        $this->container->getLogs()->setRunningContainerIds([self::CONTAINER_ID]);
    }

    /**
     * @Given this container ran previously but is not currently running
     */
    public function thisContainerRanPreviouslyButIsNotRunning()
    {
        $this->container->getContainerDetails()->setState(
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
        $this->container->getApplicationTester()->run([$command]);
    }

    /**
     * @Then I should see that this container has a status of :status
     */
    public function iShouldSeeThatThisContainerHasAStatusOf($status)
    {
        if (!preg_match('/CONTAINER_' . self::CONTAINER_ID . '.*' . preg_quote($status) . '/', $this->getApplicationOutput())) {
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
        return $this->container->getApplicationTester()->getDisplay();
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
        $this->container->getConfiguredContainerIds()->setIds(
            [self::CONTAINER_ID, self::SECOND_CONTAINER_ID]
        );
    }

    /**
     * @Given those containers are running
     */
    public function thoseContainersAreRunning()
    {
        $this->container->getContainerDetails()->setState(
            self::CONTAINER_ID,
            Container::STATE_RUNNING,
            self::FAKE_DNS
        );

        $this->container->getContainerDetails()->setState(
            self::SECOND_CONTAINER_ID,
            Container::STATE_RUNNING,
            self::FAKE_DNS
        );

        $this->container->getLogs()->setRunningContainerIds([self::CONTAINER_ID, self::SECOND_CONTAINER_ID]);
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
        $this->container->getApplicationTester()->run(['command' => $command, 'component' => self::CONTAINER_ID]);
    }

    /**
     * @Given I have a composer.json file that contains the extra domain name :domainName
     */
    public function iHaveAComposerJsonFileThatContainsTheExtraDomainName($domainName)
    {
        $composeFileContents = <<<EOF
{
  "extra": {
    "dock-cli": {
      "extra-hostname": {
        "web": ["$domainName"]
      }
    }
  }
}
EOF;

        $project = $this->container->getProject();
        $project->isolate();
        file_put_contents($project->getProjectBasePath().DIRECTORY_SEPARATOR.'composer.json', $composeFileContents);
    }

    /**
     * @When I start the application
     */
    public function iStartTheApplication()
    {
        $tester = $this->container->getApplicationTester();
        $status = $tester->run(['command' => 'start'], [
            'verbosity' => OutputInterface::VERBOSITY_DEBUG
        ]);

        if ($status != 0) {
            echo $tester->getDisplay();

            throw new \RuntimeException(sprintf(
                'Expected status code 0, got %d',
                $status
            ));
        }
    }

    /**
     * @When the container address is :address
     */
    public function theContainerAddressIs($address)
    {
        $this->container->getContainerDetails()->setState(
            self::CONTAINER_ID,
            Container::STATE_RUNNING,
            null,
            $address
        );
    }

    /**
     * @Then the domain name :domainName should be resolved as :address
     */
    public function theDomainNameShouldBeResolvedAs($domainName, $address)
    {
        $resolutions = $this->container->getResolutionWriter()->getResolutions();
        if (!array_key_exists($domainName, $resolutions)) {
            throw new \RuntimeException(sprintf(
                'No resolution found for domain name "%s"',
                $domainName
            ));
        }

        if ($resolutions[$domainName] != $address) {
            throw new \RuntimeException(sprintf(
                'Found resolution "%s" while expecting "%s"',
                $resolutions[$domainName],
                $address
            ));
        }
    }
}
