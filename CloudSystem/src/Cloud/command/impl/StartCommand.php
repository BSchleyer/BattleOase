<?php

namespace Cloud\command\impl;

use Cloud\command\Command;
use Cloud\server\ServerManager;
use Cloud\template\TemplateManager;
use Cloud\utils\CloudLogger;

class StartCommand extends Command {

    public function execute(array $args): bool {
        if (isset($args[0])) {
            $count = 1;
            if (isset($args[1])) if (is_numeric($args[1])) if (intval($args[1]) > 0) $count = intval($args[1]);

            if (($template = TemplateManager::getInstance()->getTemplate($args[0]))) {
                ServerManager::getInstance()->startServer($template, $count);
            } else {
                CloudLogger::getInstance()->error("The template §e" . $args[0] . " §rdoesn't exists!");
            }
        } else {
            CloudLogger::getInstance()->info("§c" . $this->getUsage());
        }
        return true;
    }
}