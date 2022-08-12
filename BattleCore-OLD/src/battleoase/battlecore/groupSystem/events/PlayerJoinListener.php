<?php


namespace battleoase\battlecore\groupSystem\events;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\BattlePlayer;
use battleoase\battlecore\groupSystem\api\GroupAPI;
use ceepkev77\cloudbridge\network\packet\PlayerKickPacket;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use battleoase\battlecore\groupSystem\api\TimeAPI;
use battleoase\battlecore\groupSystem\GroupSystem;
use battleoase\battlecore\groupSystem\tasks\TimeRankTask;
use pocketmine\player\Player;

class PlayerJoinListener implements Listener
{

	public function onJoin(PlayerJoinEvent $event){
		$player = $event->getPlayer();
		$playerapi = GroupSystem::getPlayerAPI();

		if ($player instanceof BattlePlayer){
			if ($player->getGroup() === "Player"){
				$message = "§cOur services are in Maintenance, please try again!";
				$pk = new PlayerKickPacket();
				$pk->playerName = $player->getName();
				$pk->reason = $message;
				$pk->sendPacket();

				$player->disconnect($message);
			}
		}

		$playerapi->setPermissions($event->getPlayer());
		$playerapi->setPrefix($event->getPlayer());

		$event->setJoinMessage("");

	}

}