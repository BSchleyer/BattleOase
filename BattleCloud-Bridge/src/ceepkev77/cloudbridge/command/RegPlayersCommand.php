<?php


namespace ceepkev77\cloudbridge\command;


use battleoase\battlecore\BattleCore;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class RegPlayersCommand extends Command
{

	public function __construct()
	{
		parent::__construct("regplayers", "See the current count of Reg Players");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		$query = BattleCore::getInstance()->getMysqlConnection()->query("SELECT COUNT(*) FROM Core.players");
		if ($query->num_rows > 0){
			while ($row = $query->fetch_assoc()){
				$sender->sendMessage(BattleCore::getPrefix() . "§7Currently §e{$row["COUNT(*)"]} §7players are registered");
			}
		}
	}

}