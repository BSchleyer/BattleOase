<?php

namespace ceepkev77\cloudbridge\command;

use ceepkev77\cloudbridge\CloudBridge;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class HubCommand extends Command
{

    public function __construct()
    {
        parent::__construct("hub", "Teleport to hub", false, []);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {

    }

}