<?php

namespace battleoase\battlecore\friendSystem\commands;

use battleoase\battlecore\BattlePlayer;
use battleoase\battlecore\friendSystem\api\FriendsAPI;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;

class FriendsCommand extends Command {

    public function __construct(string $name="friends", Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = ["friend"])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player){
        	FriendsAPI::sendFriendsUI($sender);
        }
    }
}