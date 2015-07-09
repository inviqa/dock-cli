<?php

use Dock\System\Mac\ShellCreator;

$container['system.shell_creator'] = function() {
    return new ShellCreator();
};
