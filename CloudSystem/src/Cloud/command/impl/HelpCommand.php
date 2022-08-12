<?php

namespace Cloud\command\impl;

use Cloud\Cloud;
use Cloud\command\Command;
use Cloud\utils\CloudLogger;

class HelpCommand extends Command {

    public function execute(array $args): bool {
        foreach (Cloud::getInstance()->getCommandManager()->getCommands() as $command) {
            CloudLogger::getInstance()->info("§e" . $command->getName() . " §8- §r" . $command->getDescription() . " §8- §7[§e" . implode(", ", $command->getAliases()) . "§7]");
        }
        return true;
    }
}