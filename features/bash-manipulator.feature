Feature:
  In order to customize the Docker installation
  I need to update variables in bash files

  Scenario: I can get the value of an inline variable definition
    Given I have a file that contains the following code:
    """
    DOCKER_HOST=foo
    """
    When I ask the value of the variable "DOCKER_HOST"
    Then I should get the value "foo"

  Scenario: I can get the value of an inline variable definition with quotes
    Given I have a file that contains the following code:
    """
    DOCKER_HOST='-H tcp://0.0.0.0:2376'
    """
    When I ask the value of the variable "DOCKER_HOST"
    Then I should get the value "-H tcp://0.0.0.0:2376"

  Scenario: I can get the value of an inline variable definition with quotes
    Given I have a file that contains the following code:
    """
    EXTRA_ARGS='
    --label provider=virtualbox
    --bip=172.17.42.1/24
    '
    """
    When I ask the value of the variable "EXTRA_ARGS"
    Then I should get following value:
    """
    --label provider=virtualbox
    --bip=172.17.42.1/24
    """

  Scenario: I can write a new variable in the bash script
    Given I have a file that contains the following code:
    """
    DOCKER_HOST='-H tcp://0.0.0.0:2376'
    """
    When I replace the value of the variable "DO_NOT_EXISTS" with "bar"
    Then the content of the file should be:
    """
    DOCKER_HOST='-H tcp://0.0.0.0:2376'
    DO_NOT_EXISTS='bar'
    """

  Scenario: I can replace the value of a variable with a multi-line value

    Given I have a file that contains the following code:
    """
    EXTRA_ARGS='
    --label provider=virtualbox
    --bip=172.17.42.1/24
    '
    """
    When I replace the value of the variable "EXTRA_ARGS" with the following:
    """
    --label provider=virtualbox
    --bip=172.17.42.1/24
    --foo bar
    """
    Then the content of the file should be:
    """
    EXTRA_ARGS='--label provider=virtualbox
    --bip=172.17.42.1/24
    --foo bar'
    """
