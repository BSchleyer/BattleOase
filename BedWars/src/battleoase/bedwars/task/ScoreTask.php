<?php


namespace battleoase\bedwars\task;


use battleoase\bedwars\BedWars;
use battleoase\bedwars\utils\PlayerScoreboard;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class ScoreTask extends Task
{
	public function onRun(): void
	{
		if (BedWars::getInstance()->ingame == true){
			foreach (Server::getInstance()->getOnlinePlayers() as $player){
				$scoreboard = new PlayerScoreboard();
				$scoreboard->scoreboard($player);
			}
		}
	}
}