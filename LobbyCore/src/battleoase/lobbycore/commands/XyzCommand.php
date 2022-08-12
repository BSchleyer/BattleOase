<?php


namespace battleoase\lobbycore\commands;


use battleoase\battlecore\npcSystem\NpcSystem;
use battleoase\lobbycore\player\PlayerManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Location;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\snooze\SleeperHandler;

class XyzCommand extends Command
{
	public function __construct()
	{
		parent::__construct("xyz", "Xyz Command");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if ($sender instanceof Player) {
            if(isset($args[0])) {
                NpcSystem::spawn("ceepkev77", "§bDevelopment Test", new Location(-42749.5, 49, -5893.5, Server::getInstance()->getWorldManager()->getDefaultWorld(), 0, 0), true, function (Player $player) {
                    Server::getInstance()->dispatchCommand($player, "gamepass");
                });
                NpcSystem::spawn("ceepkev77", "§4Development Test", new Location(-42753.5, 49, -5893.5, Server::getInstance()->getWorldManager()->getDefaultWorld(), 0, 0), true, function (Player $player) {
                    Server::getInstance()->dispatchCommand($player, "gamepass admin");
                });
            }
			$sender->sendMessage("X: " . $sender->getPosition()->getX());
			$sender->sendMessage("Y: " . $sender->getPosition()->getY());
			$sender->sendMessage("Z: " . $sender->getPosition()->getZ());
			$sender->sendMessage("---");
			$sender->sendMessage("Yaw: " . $sender->getLocation()->getYaw());
			$sender->sendMessage("Pitch: " . $sender->getLocation()->getPitch());

		}
	}
}