<?php

namespace ceepkev77\cloudbridge\command;

use ceepkev77\cloudbridge\network\packet\CloudPlayerAddPermissionPacket;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;

class PermissionCommand extends Command
{

    public function __construct()
    {
        parent::__construct("cloudperms", "BattleCloud", false, ["cp"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if(isset($args[1])) {
            $pk = new CloudPlayerAddPermissionPacket();
            $pk->playerName = $args[0];
            $pk->permission = $args[1];
            $pk->sendPacket();
        }
    }

}