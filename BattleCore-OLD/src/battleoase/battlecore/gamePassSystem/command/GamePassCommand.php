<?php

namespace battleoase\battlecore\gamePassSystem\command;

use battleoase\battlecore\gamePassSystem\form\admin\AdminGamePassForm;
use battleoase\battlecore\gamePassSystem\form\UnlockGamePassForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\CommandException;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;

class GamePassCommand extends Command
{

    public function __construct() {
        parent::__construct("gamepass", "GamePass", false, []);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if($sender instanceof Player) {
            if(isset($args[0])) {
                if($args[0] === "admin") {
                    $sender->sendForm(new AdminGamePassForm());
                } else {
                    $sender->sendForm(new UnlockGamePassForm());
                }
            } else {
                $sender->sendForm(new UnlockGamePassForm());
            }

        }
    }
}