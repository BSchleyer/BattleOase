<?php

namespace Cloud\command\impl;

use Cloud\command\Command;
use Cloud\template\Template;
use Cloud\template\TemplateManager;
use Cloud\utils\CloudLogger;

class CreateCommand extends Command {

    public function execute(array $args): bool {
        if (isset($args[0]) && isset($args[1])) {
            if (!TemplateManager::getInstance()->isTemplateExisting($args[1])) {
                if (strtolower($args[0]) == "proxy") {
                    CloudLogger::getInstance()->info("The proxy template §e" . $args[1] . " §7was §acreated§r!");
                    TemplateManager::getInstance()->createTemplate(new Template($args[1], 0, 2, 50, false, Template::TYPE_PROXY));
                } else if (strtolower($args[0]) == "server") {
                    CloudLogger::getInstance()->info("The server template §e" . $args[1] . " §7was §acreated§r!");
                    TemplateManager::getInstance()->createTemplate(new Template($args[1], 0, 2, 20, false, Template::TYPE_SERVER));
                } else {
                    CloudLogger::getInstance()->info("§c" . $this->getUsage());
                }
            } else {
                CloudLogger::getInstance()->error("The template §e" . $args[1] . " §ralready exists!");
            }
        } else {
            CloudLogger::getInstance()->info("§c" . $this->getUsage());
        }
        return true;
    }
}