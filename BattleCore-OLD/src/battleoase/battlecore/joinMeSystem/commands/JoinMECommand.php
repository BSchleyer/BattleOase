<?php


namespace battleoase\battlecore\joinMeSystem\commands;


use battleoase\battlecore\joinMeSystem\time\TimeAPI;
use battleoase\battlecore\joinMeSystem\utils\Utils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class JoinMECommand extends Command
{

	public function __construct()
	{
		parent::__construct("joinme", "JoinME Command", "/joinme", ["jme"]);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if ($sender instanceof Player){
			$utils = new Utils();
			$utils->onJoinMeForm($sender);
		}
	}
}