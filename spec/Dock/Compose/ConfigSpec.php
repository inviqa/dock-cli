<?php

namespace spec\Dock\Compose;

use Dock\Docker\Compose\Project;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ConfigSpec extends ObjectBehavior
{
    function let(Project $project)
    {
        $project->getComposeConfigPath()->willReturn('spec/fixtures/docker-compose.yml');
        $this->beConstructedWith($project);
    }

    function it_finds_service_on_precise_path(Project $project)
    {
        $project->getCurrentRelativePath()->willReturn('services/elasticsearch');
        $this->getCurrentService()->shouldReturn('elasticsearch');
    }

    function it_finds_service_when_in_subdirectory(Project $project)
    {
        $project->getCurrentRelativePath()->willReturn('services/elasticsearch/other');
        $this->getCurrentService()->shouldReturn('elasticsearch');
    }

    function it_finds_default_service(Project $project)
    {
        $project->getCurrentRelativePath()->willReturn('');
        $this->getCurrentService()->shouldReturn('web');
    }

    function it_finds_default_service_when_in_subdirectory(Project $project)
    {
        $project->getCurrentRelativePath()->willReturn('services/');
        $this->getCurrentService()->shouldReturn('web');
    }


    function it_finds_service_when_no_default_service_exists(Project $project)
    {
        $project->getCurrentRelativePath()->willReturn('app/config');
        $project->getComposeConfigPath()->willReturn('spec/fixtures/docker-compose-nodefault.yml');
        $this->getCurrentService()->shouldReturn('web');
    }

    function it_throws_an_exception_when_not_in_service(Project $project)
    {
        $project->getCurrentRelativePath()->willReturn('');
        $project->getComposeConfigPath()->willReturn('spec/fixtures/docker-compose-nodefault.yml');
        $this->shouldThrow('Dock\\Compose\\NotWithinServiceException')->during('getCurrentService');
    }

    function it_gets_a_list_of_services()
    {
        $this->getServices()->shouldReturn(['web', 'elasticsearch', 'other', 'mysql']);
    }
}
