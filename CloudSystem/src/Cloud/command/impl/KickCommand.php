<?php

namespace Cloud\command\impl;

use Cloud\command\Command;
use Cloud\player\PlayerManager;
use Cloud\utils\CloudLogger;

class KickCommand extends Command {

    public function execute(array $args): bool {
        if (isset($args[0])) {
            if (($player = PlayerManager::getInstance()->getPlayer($args[0])) !== null) {
                $reason = "No reason given.";
                if (isset($args[1])) {
                    unset($args[0]);
                    $reason = implode(" ", $args);
                }
                $player->kick($reason);
                CloudLogger::getInstance()->info("The player §e" . $player->getName() . " §rwas kicked!");
            } else {
                CloudLogger::getInstance()->error("The player §e" . $args[0] . " §risn't online!");
            }
        } else {
            CloudLogger::getInstance()->info("§c" . $this->getUsage());
        }
        return true;
    }
}