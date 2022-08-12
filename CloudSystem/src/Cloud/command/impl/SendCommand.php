<?php

namespace Cloud\command\impl;

use Cloud\command\Command;
use Cloud\player\PlayerManager;
use Cloud\utils\CloudLogger;

class SendCommand extends Command {

    public function execute(array $args): bool {
        if (isset($args[0]) && isset($args[1]) && isset($args[2])) {
            if (($player = PlayerManager::getInstance()->getPlayer($args[0])) !== null) {
                $message = substr(implode(" ", $args), strlen($args[0]) + strlen($args[1]) + 2, strlen(implode(" ", $args)));
                if (strtolower($args[1]) == "message") {
                    CloudLogger::getInstance()->info("The message was §asent §rto the player §e" . $player->getName() . "§r!");
                    $player->sendMessage($message);
                } else if (strtolower($args[1]) == "title") {
                    CloudLogger::getInstance()->info("The title was §asent §rto the player §e" . $player->getName() . "§r!");
                    $player->sendTitle($message);
                } else if (strtolower($args[1]) == "popup") {
                    CloudLogger::getInstance()->info("The popup was §asent §rto the player §e" . $player->getName() . "§r!");
                    $player->sendPopup($message);
                } else if (strtolower($args[1]) == "tip") {
                    CloudLogger::getInstance()->info("The tip was §asent §rto the player §e" . $player->getName() . "§r!");
                    $player->sendTip($message);
                } else if (strtolower($args[1]) == "actionbar") {
                    CloudLogger::getInstance()->info("The actionbar message was §asent §rto the player §e" . $player->getName() . "§r!");
                    $player->sendActionbarMessage($message);
                } else {
                    CloudLogger::getInstance()->info("§c" . $this->getUsage());
                }
            } else {
                CloudLogger::getInstance()->error("The player §e" . $args[0] . " §risn't online!");
            }
        } else {
            CloudLogger::getInstance()->info("§c" . $this->getUsage());
        }
        return true;
    }
}