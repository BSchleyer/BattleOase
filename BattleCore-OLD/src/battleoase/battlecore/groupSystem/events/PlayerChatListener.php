<?php


namespace battleoase\battlecore\groupSystem\events;


use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use battleoase\battlecore\groupSystem\api\TimeAPI;
use battleoase\battlecore\groupSystem\GroupSystem;
use battleoase\battlecore\groupSystem\objects\Group;

class PlayerChatListener implements Listener
{

	public function onChat(PlayerChatEvent $event){
		$player = $event->getPlayer();
		$playerapi = GroupSystem::getPlayerAPI();
		$msg = $event->getMessage();
		$event->setFormat($playerapi->getChat($player, $msg));
	}

}