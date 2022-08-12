<?php

namespace CloudBridge\commands;

use CloudBridge\api\NotifyAPI;
use CloudBridge\api\ServerAPI;
use CloudBridge\CloudBridge;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;

class CloudCommand extends Command {

    public function __construct(string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
        $this->setPermission("cloud.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if ($sender instanceof Player) {
            if ($sender->hasPermission($this->getPermission())) {
                if (isset($args[0])) {
                    if (strtolower($args[0]) == "notify") {
                        if (NotifyAPI::isInNotifyMode($sender)) {
                            $sender->sendMessage(CloudBridge::getPrefix() . "§cYou do not get any notifications anymore!");
                            NotifyAPI::setNotify($sender, false);
                        } else {
                            $sender->sendMessage(CloudBridge::getPrefix() . "§aYou now get notifications!");
                            NotifyAPI::setNotify($sender, true);
                        }
                    } else if (strtolower($args[0]) == "start") {
                        if (isset($args[1])) {
                            $count = 1;
                            if (isset($args[2])) if (is_numeric($args[2])) if (intval($args[2]) > 0) $count = intval($args[2]);
                            ServerAPI::startServer($sender, $args[1], $count);
                        } else {
                            $sender->sendMessage(CloudBridge::getPrefix() . "§c/cloud start <template> [count]");
                        }
                    } else if (strtolower($args[0]) == "stop") {
                        if (isset($args[1])) {
                            ServerAPI::stopServer($sender, $args[1]);
                        } else {
                            $sender->sendMessage(CloudBridge::getPrefix() . "§c/cloud stop <server|template|all>");
                        }
                    } else if (strtolower($args[0]) == "list") {
                        ServerAPI::listServers($sender);
                    } else if (strtolower($args[0]) == "serverinfo" || strtolower($args[0]) == "si") {
                        if (isset($args[1])) {
                            ServerAPI::serverInfo($sender, $args[1]);
                        } else {
                            $sender->sendMessage(CloudBridge::getPrefix() . "§c/cloud serverinfo <server>");
                        }
                    } else if (strtolower($args[0]) == "playerinfo" || strtolower($args[0]) == "pi") {
                        if (isset($args[1])) {
                            ServerAPI::playerInfo($sender, $args[1]);
                        } else {
                            $sender->sendMessage(CloudBridge::getPrefix() . "§c/cloud playerinfo <server>");
                        }
                    } else {
                        $sender->sendMessage(CloudBridge::getPrefix() . "§c/cloud start <template> [count]");
                        $sender->sendMessage(CloudBridge::getPrefix() . "§c/cloud stop <server>");
                        $sender->sendMessage(CloudBridge::getPrefix() . "§c/cloud serverinfo <server>");
                        $sender->sendMessage(CloudBridge::getPrefix() . "§c/cloud playerinfo <server>");
                        $sender->sendMessage(CloudBridge::getPrefix() . "§c/cloud list");
                        $sender->sendMessage(CloudBridge::getPrefix() . "§c/cloud notify");
                    }
                } else {
                    $sender->sendMessage(CloudBridge::getPrefix() . "§c/cloud start <template> [count]");
                    $sender->sendMessage(CloudBridge::getPrefix() . "§c/cloud stop <server>");
                    $sender->sendMessage(CloudBridge::getPrefix() . "§c/cloud serverinfo <server>");
                    $sender->sendMessage(CloudBridge::getPrefix() . "§c/cloud playerinfo <server>");
                    $sender->sendMessage(CloudBridge::getPrefix() . "§c/cloud list");
                    $sender->sendMessage(CloudBridge::getPrefix() . "§c/cloud notify");
                }
            } else {
                $sender->sendMessage(CloudBridge::getPrefix() . "§cYou don't have the permission to use this command!");
            }
        }
        return true;
    }
}