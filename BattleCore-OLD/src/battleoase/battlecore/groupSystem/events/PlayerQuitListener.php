<?php


namespace battleoase\battlecore\groupSystem\events;


use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use battleoase\battlecore\groupSystem\api\PlayerAPI;

class PlayerQuitListener implements Listener
{

	public function onQuit(PlayerQuitEvent $event){
		$playerapi = new PlayerAPI();
		$playerapi->unsetPermissions($event->getPlayer());
	}

}