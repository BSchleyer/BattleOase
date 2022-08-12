<?php

namespace Cloud\command\impl;

use Cloud\command\Command;
use Cloud\server\ServerManager;
use Cloud\utils\CloudLogger;

class SaveCommand extends Command {

    public function execute(array $args): bool {
        if (isset($args[0])) {
            if (($server = ServerManager::getInstance()->getServer($args[0])) !== null) {
                ServerManager::getInstance()->saveServer($server);
                CloudLogger::getInstance()->info("The server §e" . $server->getName() . " §rwas §asaved§r!");
            } else {
                CloudLogger::getInstance()->error("The server §e" . $args[0] . " §rdoesn't exists!");
            }
        } else {
            CloudLogger::getInstance()->info("§c" . $this->getUsage());
        }
        return true;
    }
}