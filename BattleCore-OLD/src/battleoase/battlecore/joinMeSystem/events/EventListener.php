<?php


namespace battleoase\battlecore\joinMeSystem\events;


use battleoase\battlecore\joinMeSystem\JoinMeSystem;
use battleoase\battlecore\joinMeSystem\utils\Utils;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;

class EventListener implements Listener
{
	public function onJoin(PlayerJoinEvent $event) {
		JoinMeSystem::$player[$event->getPlayer()->getName()] = false;
	}

	public function onQuit(PlayerQuitEvent $event) {
		Utils::deleteJoinMeByPlayerName($event->getPlayer()->getName());
	}
}