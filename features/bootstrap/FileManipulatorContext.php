<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Dock\IO\FileManipulator;
use Dock\System\Bash\BashFileManipulator;
use Fake\InMemoryFileManipulator;

class FileManipulatorContext implements Context
{
    /**
     * @var FileManipulator
     */
    private $fileManipulator;

    /**
     * @var string|null
     */
    private $value;

    /**
     * @Given I have a file that contains the following code:
     */
    public function iHaveAFileThatContainsTheFollowingCode(PyStringNode $string)
    {
        $this->fileManipulator = new InMemoryFileManipulator($string->getRaw());
    }

    /**
     * @When I replace the value of the variable :variable with :value
     */
    public function iReplaceTheValueOfTheVariableWithInTheFile($variable, $value)
    {
        (new BashFileManipulator($this->fileManipulator))->replaceValue($variable, $value);
    }

    /**
     * @When I ask the value of the variable :variable
     */
    public function iAskTheValueOfTheVariableInTheFile($variable)
    {
        $this->value = (new BashFileManipulator($this->fileManipulator))->getValue($variable);
    }

    /**
     * @When I replace the value of the variable :variable with the following:
     */
    public function iReplaceTheValueOfTheVariableInTheFileWithTheFollowing($variable, PyStringNode $string)
    {
        (new BashFileManipulator($this->fileManipulator))->replaceValue($variable, $string->getRaw());
    }

    /**
     * @Then the content of the file should be:
     */
    public function theFollowingFileShouldBeWrittenIn(PyStringNode $string)
    {
        if ($this->fileManipulator->read() != $string->getRaw()) {
            throw new \RuntimeException(sprintf(
                'Expected to have "%s" but got "%s"',
                $string->getRaw(),
                $this->fileManipulator->read()
            ));
        }
    }

    /**
     * @Then I should get the value :value
     */
    public function iShouldGetTheValue($value)
    {
        if ($this->value != $value) {
            throw new \RuntimeException(sprintf(
                'Expected to have value "%s" but got "%s"',
                $value,
                $this->value
            ));
        }
    }

    /**
     * @Then I should get following value:
     */
    public function iShouldGetFollowingValue(PyStringNode $string)
    {
        $this->iShouldGetTheValue($string->getRaw());
    }
}
