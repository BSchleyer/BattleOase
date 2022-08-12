<?php

namespace xxFLORII\Cores\Listener;

use xxFLORII\Cores\Main;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Server;

class QuitListener implements Listener {

    public function onQuit(PlayerQuitEvent $event)
    {
        $player = $event->getPlayer();
        if (Main::getInstance()->getConfig()->get("ingame") == false){
			foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer){
				$onlinePlayer->sendMessage(Main::getPrefix() . "§e{$player->getDisplayName()} §chas left the server.");
				$config = Main::getInstance()->getConfig();
				$config->set("time", 20);
			}
		}else{
			foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer){
				$onlinePlayer->sendMessage(Main::getPrefix() . "§e{$player->getDisplayName()} §chas left the server.");
			}
			Main::getCoresAPI()->deletePlayer($player);
		}
        $event->setQuitMessage("");
    }

}
