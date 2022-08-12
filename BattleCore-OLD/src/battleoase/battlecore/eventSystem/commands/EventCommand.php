<?php


namespace battleoase\battlecore\eventSystem\commands;


use battleoase\battlecore\BattleCore;
use battleoase\lobbycore\LobbyCore;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class EventCommand extends Command
{
	public function __construct()
	{
		$this->setPermission("event.command");
		parent::__construct("event", "Create Events!", "/events server|off", ["events"]);
	}

	public function isOnline(string $server)
	{
		return is_dir("/home/cloud/temp/$server");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) {
		if ($sender instanceof Player){
			if (isset($args[0]) && isset($args[1]) && isset($args[2])){
				if ($this->isOnline($args[0])){
					BattleCore::getInstance()->eventSystem->setEventServer($sender, $args[0], $args[1], $args[2]);
				}else{
					$sender->sendMessage(BattleCore::getInstance()->eventSystem->prefix . "§cServer is not Online!");
				}
			}else{
				$sender->sendMessage(BattleCore::getInstance()->eventSystem->prefix . "§cUsage: /event server title desc!");
			}
		}
	}
}