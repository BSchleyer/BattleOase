<?php

namespace ceepkev77\cloudbridge\command;

use ceepkev77\cloudbridge\CloudBridge;
use ceepkev77\cloudbridge\network\packet\CloudPlayerAddPermissionPacket;
use ceepkev77\cloudbridge\network\packet\UpdateGameServerInfoPacket;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\Server;

class ServerInfoCommand extends Command
{

    public function __construct()
    {
        parent::__construct("serverinfo", "ServerInfo Command - BattleCloud", false, []);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
            $sender->sendMessage(
                "ServerName: " . CloudBridge::getGameServer()->getName() . PHP_EOL .
                "TemplateName: " . CloudBridge::getGameServer()->getCloudGroup()->getName()
            );

    }

}