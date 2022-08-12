<?php

namespace ceepkev77\cloudbridge\listener\server;

use ceepkev77\cloudbridge\CloudBridge;
use ceepkev77\cloudbridge\listener\cloud\ProxyPlayerJoinEvent;
use ceepkev77\cloudbridge\network\packet\UpdateGameServerInfoPacket;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Server;

class PlayerJoinListener implements Listener
{

    public function onPlayerJoin(PlayerJoinEvent $event)
    {
        if(in_array($event->getPlayer()->getName(), CloudBridge::$qeueuPlayer)) {
            unset(CloudBridge::$qeueuPlayer[$event->getPlayer()->getName()]);
            $event = new ProxyPlayerJoinEvent($event->getPlayer()->getName(), $event->getPlayer());
            $event->call();
        }
        //Update Server Stats
        $packet = new UpdateGameServerInfoPacket();
        $packet->type = $packet->TYPE_UPDATE_PLAYER_COUNT;
        $packet->value = count(Server::getInstance()->getOnlinePlayers());
        $packet->sendPacket();
    }

}