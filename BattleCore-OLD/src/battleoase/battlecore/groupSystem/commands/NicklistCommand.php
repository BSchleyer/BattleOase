<?php


namespace battleoase\battlecore\groupSystem\commands;


use battleoase\battlecore\BattleCore;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class NicklistCommand extends Command
{

	public function __construct()
	{
		parent::__construct("nicklist", "NicklistCommand", "/nicklist");
		$this->setPermission("supporter");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if($sender->hasPermission("supporter")) {
			$nickdata = null;
			$sender->sendMessage("§7Nick-List on ".Server::getInstance()->getMotd());
			foreach (Server::getInstance()->getOnlinePlayers() as $player) {
				$name = $player->getName();
				$result =BattleCore::getInstance()->getMysqlConnection()->query("SELECT * From Core.group_players WHERE player_name = '$name' and nick != 'NULL'");
				if ($result->num_rows > 0) {
					while ($row = $result->fetch_assoc()) {
						$nickdata = $row["nick"];
					}
				}
				if ($nickdata != null) {
					$sender->sendMessage("§e" . $player->getName() . " §7=> §e" . $nickdata);
					$sender->sendMessage("§r§f§l――――――――――――――――――――――――");


				}
			}
		}
	}
}