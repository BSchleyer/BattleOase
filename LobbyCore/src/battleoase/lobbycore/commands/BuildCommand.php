<?php


namespace battleoase\lobbycore\commands;


use battleoase\lobbycore\player\PlayerManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\GameMode;
use pocketmine\player\Player;

class BuildCommand extends Command
{
	public function __construct()
	{
		parent::__construct("build", "Enable/Disable Build Command");
		$this->setPermission("lobby.build");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if ($sender->hasPermission("lobby.build")){
			if($sender instanceof Player) {
				$lobbyPlayer = PlayerManager::getPlayer($sender);
				switch ($lobbyPlayer->isBuild()) {
					case 0: {
						$lobbyPlayer->enableBuild();
						$lobbyPlayer->getPlayer()->setGamemode(GameMode::CREATIVE());
						$lobbyPlayer->getPlayer()->getInventory()->clearAll();
						break;
					}
					case 1: {
						$lobbyPlayer->disableBuild();
						$lobbyPlayer->getPlayer()->setGamemode(GameMode::SURVIVAL());
						$lobbyPlayer->getPlayer()->getInventory()->clearAll();
						PlayerManager::getPlayer($sender)->giveItems();
						break;
					}
				}
			}
		}
	}
}