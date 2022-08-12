<?php

namespace xxFLORII\Cores\Listener;

use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\world\Position;
use xxFLORII\Cores\Main;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;

class PlaceListener implements Listener
{
	public function onPlace(BlockPlaceEvent $event)
	{
		$player = $event->getPlayer();

		$config = Main::getInstance()->getConfig();
		if ($config->get("ingame") === false) {
			$event->cancel();
		} else {
			$block = $event->getBlock();

			$level = Server::getInstance()->getWorldManager()->getWorldByName($config->get("Arena"));
			$af = new Config(Main::getInstance()->getDataFolder() . "/" . $config->get("Arena") . ".yml", Config::YAML);

			$location = new Position($af->get("s1x"), $af->get("s1y") + 1, $af->get("s1z"), $level);
			$location2 = new Position($af->get("s2x"), $af->get("s2y") + 1, $af->get("s2z"), $level);
			if ($player->getPosition()->distance($location) <= 5){
				$player->sendMessage(Main::getPrefix() . "§cYou can't place blocks here.");
				$event->cancel();
				return;
			}

			if ($player->getPosition()->distance($location2) <= 5){
				$player->sendMessage(Main::getPrefix() . "§cYou can't place blocks here.");
				$event->cancel();
				return;
			}
			array_push(Main::$placedBlocks, [$block->getPosition()->x, $block->getPosition()->y, $block->getPosition()->z]);
		}
	}
}
