<?php

namespace battleoase\battlecore\coinSystem\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\CommandException;
use pocketmine\lang\Translatable;

class CoinCommand extends Command {

    public function __construct() {
        parent::__construct("coin", "CoinSystem", false, []);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        //Todo: Permissions
    }
}