<?php

namespace Cloud\command\impl;

use Cloud\command\Command;
use Cloud\server\ServerManager;
use Cloud\utils\CloudLogger;

class DispatchCommand extends Command {

    public function execute(array $args): bool {
        if (isset($args[0]) && isset($args[1])) {
            if (($server = ServerManager::getInstance()->getServer($args[0])) !== null) {
                unset($args[0]);
                $commandLine = implode(" ", $args);
                ServerManager::getInstance()->dispatchCommand($server, $commandLine);
                CloudLogger::getInstance()->info("The command was §asent §rto the server §e" . $server->getName() . "§r!");
            } else {
                CloudLogger::getInstance()->error("The server §e" . $args[0] . " §rdoesn't exists!");
            }
        } else {
            CloudLogger::getInstance()->info("§c" . $this->getUsage());
        }
        return true;
    }
}