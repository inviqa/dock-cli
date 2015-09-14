Feature:
  In order to use the application with a custom domain name
  As a developer
  When I start an application, I want dock-cli to create a corresponding line in the `/etc/hosts` file

  Scenario:
    Given I have a Docker Compose file that contains one container
    And this container is not running
    And I have a composer.json file that contains the extra domain name "my-custom.domain.name"
    And the container address is "172.17.0.34"
    When I start the application
    Then the domain name "my-custom.domain.name" should be resolved as "172.17.0.34"
