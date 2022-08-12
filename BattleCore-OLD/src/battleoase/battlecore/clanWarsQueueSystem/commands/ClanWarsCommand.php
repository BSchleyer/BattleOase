<?php


namespace battleoase\battlecore\clanWarsQueueSystem\commands;


use battleoase\battlecore\BattlePlayer;
use battleoase\battlecore\clanWarsQueueSystem\api\ClanWarsAPI;
use battleoase\battlecore\clanWarsQueueSystem\ClanWarsQueueSystem;
use battleoase\battlecore\clanWarsQueueSystem\form\ClanWarsForm;
use Frago9876543210\EasyForms\elements\Dropdown;
use Frago9876543210\EasyForms\forms\CustomForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\CommandException;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;

class ClanWarsCommand extends Command
{

    public function __construct()
    {
        parent::__construct("clanwars", "ClanWarsQueueSystem Queue", false, ["cwbw", "cw"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender instanceof Player) {
            if (isset($args[0])) {
                if (is_int((int)$args[0])) {
					ClanWarsQueueSystem::sendQueue($sender, $args[0]);
                }
            } else {
				$sender->sendForm(new ClanWarsForm());
            }
        }
    }
}