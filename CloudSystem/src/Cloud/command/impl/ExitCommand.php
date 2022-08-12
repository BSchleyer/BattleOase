<?php

namespace Cloud\command\impl;

use Cloud\Cloud;
use Cloud\command\Command;

class ExitCommand extends Command {

    public function execute(array $args): bool {
        Cloud::getInstance()->shutdown();
        return true;
    }
}