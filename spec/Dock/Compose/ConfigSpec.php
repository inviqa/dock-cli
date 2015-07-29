<?php

namespace spec\Dock\Compose;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ConfigSpec extends ObjectBehavior
{
    function it_finds_service_on_precise_path()
    {
        $this->beConstructedWith('spec/fixtures/docker-compose.yml');
        $this->getCurrentService('services/elasticsearch')->shouldReturn('elasticsearch');
    }

    function it_finds_service_when_in_subdirectory()
    {
        $this->beConstructedWith('spec/fixtures/docker-compose.yml');
        $this->getCurrentService('services/elasticsearch/other')->shouldReturn('elasticsearch');
    }

    function it_finds_default_service()
    {
        $this->beConstructedWith('spec/fixtures/docker-compose.yml');
        $this->getCurrentService('')->shouldReturn('web');
    }

    function it_finds_default_service_when_in_subdirectory()
    {
        $this->beConstructedWith('spec/fixtures/docker-compose.yml');
        $this->getCurrentService('services/')->shouldReturn('web');
    }


    function it_finds_service_when_no_default_service_exists()
    {
        $this->beConstructedWith('spec/fixtures/docker-compose.yml');
        $this->getCurrentService('app/config')->shouldReturn('web');
    }

    function it_throws_an_exception_when_not_in_service()
    {
        $this->beConstructedWith('spec/fixtures/docker-compose-nodefault.yml');
        $this->shouldThrow('\Exception')->during('getCurrentService', array(''));
    }
}
