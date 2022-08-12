<?php


namespace battleoase\bedwars\commands;


use battleoase\bedwars\BedWars;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class BuildCommand extends Command
{

	public function __construct()
	{
		parent::__construct("build", "BedWars Build Command");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if ($sender instanceof Player){
			if (BedWars::getInstance()->getPlayerManager()->getPlayer($sender)->getBuildMode() === false){
				BedWars::getInstance()->getPlayerManager()->getPlayer($sender)->setBuildMode(true);
				$sender->sendTitle("§aBuild-Mode", "§aactivated");
			}else{
				BedWars::getInstance()->getPlayerManager()->getPlayer($sender)->setBuildMode(false);
				$sender->sendTitle("§aBuild-Mode", "§cdeactivated");
			}
		}
	}

}