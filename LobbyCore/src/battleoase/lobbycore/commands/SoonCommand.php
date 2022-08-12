<?php


namespace battleoase\lobbycore\commands;


use battleoase\battlecore\BattlePlayer;
use battleoase\lobbycore\LobbyCore;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class SoonCommand extends Command
{

	public function __construct()
	{
		parent::__construct("soon", " ");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if ($sender instanceof BattlePlayer){
			if (!$sender->hasCooldown("SOON_COMMAND")){
				$sender->sendMessage(LobbyCore::PREFIX . "§cNa huch! Du bist gerade auf ein §eFeature §cgekommen, was es noch nicht gibt! Komm später wieder!");
				$sender->resetCooldown("SOON_COMMAND", 20 * 4);
			}
		}
	}

}