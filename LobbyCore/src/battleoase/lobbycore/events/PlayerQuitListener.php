<?php


namespace battleoase\lobbycore\events;


use battleoase\lobbycore\LobbyCore;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;

class PlayerQuitListener implements Listener
{
	public function onQuit(PlayerQuitEvent $event){
		$event->setQuitMessage("");
		LobbyCore::getInstance()->getPlayerManager()->unregisterPlayer($event->getPlayer());
	}
}