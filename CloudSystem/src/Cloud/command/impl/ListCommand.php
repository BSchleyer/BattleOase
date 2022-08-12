<?php

namespace Cloud\command\impl;

use Cloud\command\Command;
use Cloud\player\PlayerManager;
use Cloud\server\ServerManager;
use Cloud\server\status\ServerStatus;
use Cloud\template\Template;
use Cloud\template\TemplateManager;
use Cloud\utils\CloudLogger;

class ListCommand extends Command {

    public function execute(array $args): bool {
        if (isset($args[0])) {
            if (strtolower($args[0]) == "templates") {
                $this->sendTemplates();
            } else if (strtolower($args[0]) == "servers") {
                $this->sendServers();
            } else if (strtolower($args[0]) == "players") {
                $this->sendPlayers();
            } else {
                CloudLogger::getInstance()->info("§c" . $this->getUsage());
            }
        } else {
            $this->sendServers();
        }
        return true;
    }

    private function statusString(int $status): string {
        if ($status == ServerStatus::STATUS_STARTING) return "§2STARTING";
        else if ($status == ServerStatus::STATUS_STARTED) return "§aSTARTED";
        else if ($status == ServerStatus::STATUS_STOPPING) return "§4STOPPING";
        else if ($status == ServerStatus::STATUS_STOPPED) return "§cSTOPPED";
        return "";
    }

    private function sendTemplates() {
        CloudLogger::getInstance()->info("Templates: §8(§e" . count(TemplateManager::getInstance()->getTemplates()) . "§8)");
        foreach (TemplateManager::getInstance()->getTemplates() as $template) {
            CloudLogger::getInstance()->info($template->getName() .
                " §8| §rMinServers: §e" . $template->getMinServers() .
                " §8| §rMaxServers: §e" . $template->getMaxServers() .
                " §8| §rMaxPlayers: §e" . $template->getMaxPlayers() .
                " §8| §rAutoStart: §r" . ($template->isAutoStart() ? "§aON" : "§cOFF") .
                " §8| §rType: §r" . ($template->getType() == Template::TYPE_SERVER ? "§eSERVER" : "§cPROXY")
            );
        }
    }

    private function sendServers() {
        CloudLogger::getInstance()->info("Servers: §8(§e" . count(ServerManager::getInstance()->getServers()) . "§8)");
        foreach (ServerManager::getInstance()->getServers() as $server) {
            CloudLogger::getInstance()->info($server->getName() .
                " §8| §rPort: §e" . $server->getPort() .
                " §8| §rPlayers: §e" . $server->getPlayersCount() .
                " §8| §rMaxPlayers: §e" . $server->getTemplate()->getMaxPlayers() .
                " §8| §rTemplate: " . ($server->getTemplate()->getType() == Template::TYPE_SERVER ? "§e" . $server->getTemplate()->getName() : "§c" . $server->getTemplate()->getName()) .
                " §8| §rServerStatus: §r" . $this->statusString($server->getServerStatus())
            );
        }
    }

    private function sendPlayers() {
        CloudLogger::getInstance()->info("Players: §8(§e" . count(PlayerManager::getInstance()->getPlayers()) . "§8)");
        foreach (PlayerManager::getInstance()->getPlayers() as $player) {
            CloudLogger::getInstance()->info($player->getName() .
                " §8| §rHost: §e" . $player->getAddress() .
                " §8| §rUUID: §e" . $player->getUuid() .
                ($player->getXuid() !== "" ? " §8| §rXUID: §e" . $player->getXuid() : "") .
                " §8| §rCurrentServer: §e" . $player->getCurrentServer() .
                " §8| §rCurrentProxy: §c" . ($player->getCurrentProxy() == "" ? "-" : $player->getCurrentProxy())
            );
        }
    }
}