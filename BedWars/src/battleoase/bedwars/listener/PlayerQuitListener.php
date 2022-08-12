<?php


namespace battleoase\bedwars\listener;


use battleoase\battlecore\BattlePlayer;
use battleoase\bedwars\api\TeamAPI;
use battleoase\bedwars\BedWars;
use battleoase\bedwars\caches\TeamCache;
use battleoase\bedwars\classes\Team;
use ceepkev77\cloudbridge\network\packet\PlayerMovePacket;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerToggleFlightEvent;
use pocketmine\item\Bed;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class PlayerQuitListener implements Listener
{
    public function onQuit(PlayerQuitEvent $event){
		BedWars::getInstance()->getPlayerManager()->getPlayer($event->getPlayer())->removeTeam();
        BedWars::getInstance()->getPlayerManager()->unregisterPlayer($event->getPlayer());
		BedWars::$i--;
		$event->setQuitMessage("");

		if (BedWars::getInstance()->ingame === false){
			BedWars::getInstance()->countdown = 20;
		}

		Server::getInstance()->broadcastMessage("§7[§4-§7] §7" . $event->getPlayer()->getNameTag() . "§7 (§a" . BedWars::$i  . "§e/§c" . Server::getInstance()->getMaxPlayers(). "§7)");
    }

	public function onExplode(EntityExplodeEvent $e) {
		$e->cancel();
	}


}