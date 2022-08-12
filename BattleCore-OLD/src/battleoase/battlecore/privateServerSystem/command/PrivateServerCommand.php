<?php


namespace battleoase\battlecore\privateServerSystem\command;


use battleoase\battlecore\privateServerSystem\PrivateServerForms;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;

class PrivateServerCommand extends Command
{

    public function __construct()
    {
        parent::__construct("privateserver", "PrivateServer", false, ["ps"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender instanceof Player) {
            PrivateServerForms::sendPrivateServerUi($sender);
        }
    }

}