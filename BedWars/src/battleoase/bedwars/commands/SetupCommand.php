<?php


namespace battleoase\bedwars\commands;


use battleoase\bedwars\api\TeamAPI;
use battleoase\bedwars\BedWars;
use ceepkev77\cloudbridge\CloudBridge;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\Bed;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\world\World;
use xxFLORII\Cores\Main;

class SetupCommand extends Command
{

	private string $arena;

	public function __construct()
	{
		parent ::__construct("setup", "Setup Command", "/setup <world>");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		$player = $sender;
		if ($player instanceof Player) {
			if(isset($args[0]) && $args[0] == "setarena"){
				$arena = $args[1];
				$maps = BedWars::getInstance()->getConfig()->get("registeredMaps", []);
				$maps[] = $args[1];
				BedWars::getInstance()->getConfig()->set("registeredMaps", $maps);
				$this->arena = $arena;

				$sender->sendMessage(BedWars::PREFIX."Du hast Erfolgreich die Arena " . $arena . " erstellt!");
				Server::getInstance()->getWorldManager()->loadWorld($args[1]);
				$spawn = Server::getInstance()->getWorldManager()->getWorldByName($args[1])->getSafeSpawn();
				Server::getInstance()->getWorldManager()->getWorldByName($args[1])->loadChunk($spawn->getX(), $spawn->getZ());
				$sender->teleport($spawn, 0, 0);

				$sender->sendMessage(BedWars::PREFIX . "§aSelected arena §d{$args[1]}§8.");
				$sender->setGamemode(GameMode::CREATIVE());

			}  elseif (isset($args[0]) && isset($args[1]) && $args[0] == "setspawn") {
				$team = $args[1];
				if(isset($this->arena)) {
						if(in_array($team, BedWars::getInstance()->getConfig()->get("teams", ["RED", "BLUE"]))) {
						$this->setSpawn($this->arena, $team, $player);
						$player->sendMessage(BedWars::PREFIX."§aDu hast den Spawn [ID=" . $team  . "] §agesetzt!");
					} else {
						$sender->sendMessage(BedWars::PREFIX."Das Team existiert nicht.");
					}
				}

			}
		}
	}

	public function setSpawn($arena, $team, Player $player) {
		$config = new Config(BedWars::getInstance()->getDataFolder() . "/arena/{$arena}.yml",  Config::YAML);
		$config->setNested("Spawn.{$team}.level", $arena);
		$config->setNested("Spawn.{$team}.x", $player->getPosition()->getX());
		$config->setNested("Spawn.{$team}.y", $player->getPosition()->getY());
		$config->setNested("Spawn.{$team}.z", $player->getPosition()->getZ());
		$config->setNested("Spawn.{$team}.yaw", $player->getLocation()->getYaw());
		$config->setNested("Spawn.{$team}.pitch", $player->getLocation()->getPitch());
		$config->save();
	}
}