Feature: Viewing container logs
  In order to know what my containers are up to
  As a developer
  I need to view the logs from my running containers

  Background:
    Given I have a Docker Compose file that contains one container

  Scenario: Container in config and running
    Given this container is running
    When I run the "logs" command
    Then I should see that this container's logs