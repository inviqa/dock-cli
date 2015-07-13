Feature: Viewing container logs
  In order to know what my containers are up to
  As a developer
  I need to view the logs from my running containers

  Background:
    Given I have a Docker Compose file that contains two containers

  Scenario: Container in config and running
    Given those containers are running
    When I run the "logs" command
    Then I should see those container's logs

  Scenario: Container in config and running
    Given those containers are running
    When I run the "logs" command for one of the components
    Then I should see this container's logs