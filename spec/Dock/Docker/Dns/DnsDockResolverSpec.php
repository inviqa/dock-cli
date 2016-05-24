<?php

namespace spec\Dock\Docker\Dns;

use Dock\Docker\Dns\ContainerAddressResolver;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DnsDockResolverSpec extends ObjectBehavior
{
    function it_is_a_container_address_resolve()
    {
        $this->shouldImplement('Dock\Docker\Dns\ContainerAddressResolver');
    }

    function it_returns_the_resolution_with_just_the_image_name()
    {
        $this->getDnsByContainerNameAndImage('container', 'image')->shouldContain('image.docker');
    }

    function it_returns_the_container_specific_resolution()
    {
        $this->getDnsByContainerNameAndImage('container', 'image')->shouldContain('container.image.docker');
    }

    function it_removes_the_tag_in_image_name()
    {
        $this->getDnsByContainerNameAndImage('container', 'image:latest')->shouldContain('container.image.docker');
    }

    function it_removes_the_slash_in_image_name()
    {
        $this->getDnsByContainerNameAndImage('container', 'someone/image:latest')->shouldContain('container.someone_image.docker');
    }
}
