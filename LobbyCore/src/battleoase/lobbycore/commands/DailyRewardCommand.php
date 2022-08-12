<?php


namespace battleoase\lobbycore\commands;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\BattlePlayer;
use battleoase\lobbycore\LobbyCore;
use Frago9876543210\EasyForms\elements\Button;
use Frago9876543210\EasyForms\forms\MenuForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use Reward\Main;

class DailyRewardCommand extends Command
{

	public function __construct()
	{
		parent::__construct("dailyreward", "DailyRewardCommand");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if ($sender instanceof BattlePlayer){
			if (!$sender->hasCooldown("CLAIM_REWARD")){
				$db = Main::getDb();
				if ($db->canClaimReward($sender)){
					$db->claimReward($sender);
				}
				$sender->resetCooldown("CLAIM_REWARD", 20 * 5);
			}
		}
	}

}