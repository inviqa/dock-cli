<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines application features from the specific context.
 */
class SmokeContext implements Context, SnippetAcceptingContext
{
    private $fs;

    private $lastOutput;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $this->fs = new \Symfony\Component\Filesystem\Filesystem();
    }

    /**
     * @Given I have a Docker Compose file that contains one container
     */
    public function iHaveADockerComposeFileThatContainsOneContainer()
    {
        $path = __DIR__ . '/../../tmp/';
        $this->fs->remove($path);
        $this->fs->mkdir($path);
        chdir($path);

        $config = <<<EOF
memcached:
  image: memcached
EOF;

        $this->fs->dumpFile('docker-compose.yml', $config);
    }

    /**
     * @Given this container is running
     */
    public function thisContainerIsRunning()
    {
        $this->executeCommand('up');
    }

    /**
     * @When I run the :command command
     */
    public function iRunTheCommand($command)
    {
        $this->lastOutput = $this->executeCommand($command);
    }

    /**
     * @Then I should see that this container has a status of :status
     */
    public function iShouldSeeThatThisContainerHasAStatusOf($status)
    {
        if (!preg_match('/|\s+' . preg_quote($status) . '\s+|$/', $this->lastOutput)) {
            throw new \Exception("Container status was not $status");
        }
    }

    /**
     * @Then I should see the DNS resolution of the container
     */
    public function iShouldSeeTheDnsResolutionOfTheContainer()
    {
        if (!preg_match('/|\s+memcached.docker\s+|$/', $this->lastOutput)) {
            throw new \Exception('Container DNS was not displayed as expected memcached.docker');
        }
    }

    /**
     * @param string $command
     * @return string
     */
    private function executeCommand($command)
    {
        return shell_exec(__DIR__ . '/../../bin/dock-cli ' . escapeshellcmd($command));
    }
}
