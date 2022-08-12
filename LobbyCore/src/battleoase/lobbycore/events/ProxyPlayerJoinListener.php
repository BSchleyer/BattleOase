<?php


namespace battleoase\lobbycore\events;


use battleoase\battlecore\discordSystem\DiscordManager;
use ceepkev77\cloudbridge\listener\cloud\ProxyPlayerJoinEvent;
use pocketmine\event\Listener;

class ProxyPlayerJoinListener implements Listener
{
	public function onProxyJoin(ProxyPlayerJoinEvent $event){
		$name = $event->getPlayerName();
		$player = $event->getPlayer();

	}
}