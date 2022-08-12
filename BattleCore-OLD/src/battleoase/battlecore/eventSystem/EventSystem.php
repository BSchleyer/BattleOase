<?php


namespace battleoase\battlecore\eventSystem;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\BattlePlayer;
use battleoase\battlecore\eventSystem\commands\EventCommand;
use battleoase\battlecore\utils\BPlugin;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;

class EventSystem extends BPlugin
{
	public string $prefix = "§eEvents §8» §7";

	public function __construct() {
		Server::getInstance()->getCommandMap()->register(Server::getInstance()->getName(), new EventCommand());
	}


	public function setEventServer(Player $player, string $server, string $title = "NONE", string $description = "NONE") {
		$date = new \DateTime("now", new \DateTimeZone("Europe/Berlin"));
		$format = $date->format("H:i:s-d.m.Y");
		$createdBy = $player->getName();

		BattleCore::getInstance()->getMysqlConnection()->query("INSERT INTO Core.events(`eventServer`, `eventTitle`, `eventDescription`, `createdBy`, `createdAt`) VALUES ('$server', '$title', '$description', '$createdBy', '$format')");
	}

	/**
	 * Function getEventServer
	 * @return array|string
	 */
	public function getEventServer() {
		$result = BattleCore::getInstance()->getMysqlConnection()->query("SELECT * FROM Core.events");
		if ($result->num_rows > 0){
			return mysqli_fetch_all ($result, MYSQLI_ASSOC);
		}else{
			return null;
		}
	}

	public function isEventServer(string $server): ?bool {
		$result = BattleCore::getInstance()->getMysqlConnection()->query("SELECT * FROM Core.events WHERE eventServer='$server'");
		if ($result->num_rows > 0){
			return true;
		}else{
			return false;
		}
	}

	public function deleteEventServer(string $server){
		BattleCore::getInstance()->getMysqlConnection()->query("DELETE * FROM Core.events WHERE eventServer='$server'");
	}
}