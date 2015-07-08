Feature: Viewing status of containers
  In order to access my application
  As a developer
  I need to see information about my running containers

  Background:
    Given I have a Docker Compose file that contains one container

    @smoke
  Scenario: Container in config and running
    Given this container is running
    When I run the "ps" command
    Then I should see that this container has a status of "running"
    And I should see the DNS resolution of the container

  Scenario: Container in config and not running
    Given this container ran previously but is not currently running
    When I run the "ps" command
    Then I should see that this container has a status of "exited"
