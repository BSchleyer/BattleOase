<?php


namespace battleoase\battlecore\coinSystem\commands;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\BattlePlayer;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class CoinsCommand extends Command
{
	public function __construct()
	{
		parent::__construct("coins", "Coins Command", "/coins", ["money", "coin"]);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if ($sender instanceof BattlePlayer){
			$sender->sendMessage(BattleCore::getPrefix() . BattleCore::getInstance()->getLanguageSystem()->translate($sender, "coins.command.message", [
				"{COINS}" => $sender->getCoins()
				]));
		}
	}
}