<?php

namespace xxFLORII\Cores\Listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Server;
use xxFLORII\Cores\API\CoresAPI;
use xxFLORII\Cores\Main;

class PlayerJoinListener implements Listener {

    public function onJoin(PlayerJoinEvent $event){
        $event->setJoinMessage("");
        $player = $event->getPlayer();
        foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer){
            $onlinePlayer->sendMessage(Main::getPrefix() . "§e{$player->getDisplayName()} §ahas joined the game.");
        }
		Main::getCoresAPI()->randomTeam($player);
		CoresAPI::giveLobbyItems($player);
    }
}