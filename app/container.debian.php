<?php

use Dock\Installer\DNS;

require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'container.linux.php';

$container['installer.dns.docker_routing'] = function($c) {
    return new DNS\Linux\Debian\DockerRouting($c['console.user_interaction'], $c['process.interactive_runner']);
};
