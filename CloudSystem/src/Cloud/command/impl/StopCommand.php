<?php

namespace Cloud\command\impl;

use Cloud\command\Command;
use Cloud\server\ServerManager;
use Cloud\template\TemplateManager;
use Cloud\utils\CloudLogger;

class StopCommand extends Command {

    public function execute(array $args): bool {
        if (isset($args[0])) {
            if (strtolower($args[0]) == "all") {
                CloudLogger::getInstance()->info("Stopping all servers...");
                ServerManager::getInstance()->stopAll();
            } else {
                if (($server = ServerManager::getInstance()->getServer($args[0])) !== null) {
                    ServerManager::getInstance()->stopServer($server);
                } else if (($template = TemplateManager::getInstance()->getTemplate($args[0])) !== null) {
                    CloudLogger::getInstance()->info("Stopping all servers with the template §e" . $template->getName() . "§r...");
                    ServerManager::getInstance()->stopTemplate($template);
                } else {
                    CloudLogger::getInstance()->info("§c" . $this->getUsage());
                }
            }
        } else {
            CloudLogger::getInstance()->info("§c" . $this->getUsage());
        }
        return true;
    }
}