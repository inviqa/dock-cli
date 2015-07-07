Feature:
  In order to access my application
  As a developer
  I need to see information about my running containers

  Scenario:
    Given I have a Docker Compose file that contains one container
    When I run the "ps" command
    Then I should see the DNS resolution of the container

  Scenario:
    Given I have a Docker Compose file that contains one container
    And this container is not running
    When I run the "ps" command
    Then I should see that this container has a status of "exited"

  Scenario:
    Given I have a Docker Compose file that contains one container
    And this container is running
    When I run the "ps" command
    Then I should see that this container has a status of "running"


