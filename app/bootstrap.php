<?php

use Pimple\Container;
use Dock\Cli\Application;

$container = new Container();

$container['application'] = function ($c) {
    return new Application('Dock CLI', '@package_version@');
};

return $container;
