<?php


namespace battleoase\lobbycore\utils;


use battleoase\battlecore\BattleCore;
use battleoase\lobbycore\LobbyCore;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\world\particle\FloatingTextParticle;

class Utils
{

	public function spawnWelcomeInfo() {
		$cfg = new Config("/home/cloud/data/lobby/welcomeInfo.yml", 2);

		$world = LobbyCore::getInstance()->getServer()->getWorldManager()->getDefaultWorld();
		$pos = new Vector3($cfg->get("x"), $cfg->get("y"), $cfg->get("z"));

		$particle = new FloatingTextParticle($this->getLobbyText());
		$world->addParticle($pos, $particle);
	}

	public function setSpawnOfWelcomeInfo(Player $player) {
		$cfg = new Config("/home/cloud/data/lobby/welcomeInfo.yml", 2);
		$cfg->set("x", $player->getPosition()->getX());
		$cfg->set("y", $player->getPosition()->getY());
		$cfg->set("z", $player->getPosition()->getZ());
		$cfg->save();
	}

	public function getLobbyText() {
		$text = "§3•§b● §b§lBattleOase.NET §b●§3•";

		$text .= "\n\n§7Welcome to the Server!";
		return $text;
	}
}