<?php

namespace xxFLORII\Cores\Listener;

use xxFLORII\Cores\API\Forms;
use xxFLORII\Cores\Main;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as Color;

class InteractListener implements Listener {

    public function onInteract(PlayerInteractEvent $event)
    {

		$player = $event->getPlayer();
		$block = $event->getBlock();
		$pos = $block->getPosition();
		$tile = $player->getWorld()->getTile($pos);
		$config = Main::getInstance()->getConfig();
		$item = $player->getInventory()->getItemInHand();
		$af = new Config(Main::getInstance()->getDataFolder() . "/" . $config->get("Arena") . ".yml", Config::YAML);
		if ($item->getCustomName() === Color::RED . "Settings") {
			//ToDo: Add Settings
		} else if ($item->getCustomName() === Color::YELLOW . "Teams") {
			Forms::sendTeamUI($player);
		}
    }
    
}
