<?php

namespace xxFLORII\Cores\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use xxFLORII\Cores\Main;

class StartCommand extends Command {

    public function __construct()
    {
        parent::__construct("start", "Start Command", "/start", ["skip"]);
        $this->setPermission("premium");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player){
            $sender->sendMessage("§cYou cannot use this command in the console.");
            return;
        }

        if (!$this->testPermissionSilent($sender)){
            $sender->sendMessage("§cYou don't have enough permissions to execute this command.");
            return;
        }

        $config = Main::getInstance()->getConfig();
        if ($config->get("time") > 6){
            $config->set("time", 6);
            $config->save();
            $sender->sendMessage(Main::getPrefix() . "§aYou have started the game.");
        } else {
            $sender->sendMessage(Main::getPrefix() . "§cThis game is already starting.");
        }
    }
}