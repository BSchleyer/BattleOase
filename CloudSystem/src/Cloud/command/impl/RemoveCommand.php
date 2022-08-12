<?php

namespace Cloud\command\impl;

use Cloud\command\Command;
use Cloud\template\TemplateManager;
use Cloud\utils\CloudLogger;

class RemoveCommand extends Command {

    public function execute(array $args): bool {
        if (isset($args[0])) {
            if (($template = TemplateManager::getInstance()->getTemplate($args[0])) !== null) {
                CloudLogger::getInstance()->info("The template §e" . $template->getName() . " §rwas §cremoved§r!");
                TemplateManager::getInstance()->removeTemplate($template);
            } else {
                CloudLogger::getInstance()->error("The template §e" . $args[0] . " §rdoesn't exists!");
            }
        } else {
            CloudLogger::getInstance()->info("§c" . $this->getUsage());
        }
        return true;
    }
}