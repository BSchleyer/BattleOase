<?php

namespace syntaxpe\knockffa\events;

use battleoase\battlecore\BattleCore;
use battleoase\bedwars\api\TeamAPI;
use battleoase\bedwars\BedWars;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use syntaxpe\knockffa\KnockFFA;

class EntityDamageListener implements Listener
{

	public function onEntity(EntityDamageByEntityEvent $event)
	{
		$player = $event->getEntity();
		$damager = $event->getDamager();

		if ($player instanceof Player) {
			if($damager instanceof Player) {
				$bwPlayer = KnockFFA::getInstance()->getPlayerManager()->getPlayer($player->getName());
				$bwDamager = KnockFFA::getInstance()->getPlayerManager()->getPlayer($damager->getName());
				if (KnockFFA::getInstance()->ingame == false) {
					$event->cancel();
				}
					KnockFFA::getInstance()->lastdamager[$player->getName()] = $damager->getName();
					if ($player->getGamemode() === GameMode::SURVIVAL()) {
						if ($player->getHealth() <= $event->getFinalDamage()) {
							$event->cancel();
							Server::getInstance()->broadcastMessage(KnockFFA::PREFIX."§a" . $player->getName() . " §cwas killed by §c" .
								$damager->getName());
							BattleCore::getInstance()->statsSystem->addKill($damager, "KnockFFA");
							BattleCore::getInstance()->statsSystem->addDeath($player, "KnockFFA");
						}
					}
			}
		} else {
			if ($damager instanceof Player) {
				//KnockFFA::getInstance()->getPlayerManager()->getPlayer($damager->getName())->getShopMenu()->send($damager);
				$event->cancel();
			}
		}
	}

	public function onDamage(EntityDamageEvent $event)
	{
		$player = $event->getEntity();

		if ($player instanceof Player) {
			$bwPlayer = KnockFFA::getInstance()->getPlayerManager()->getPlayer($player->getName());
			if ($player->getGamemode() === GameMode::CREATIVE()) {
				$event->cancel();
			}
			if ($event->getCause() === EntityDamageEvent::CAUSE_FALL || $event->getCause() == EntityDamageEvent::CAUSE_BLOCK_EXPLOSION || $event->getCause() == EntityDamageEvent::CAUSE_ENTITY_EXPLOSION) {
				if (KnockFFA::getInstance()->saveDamager == true) {
					$event->cancel();
				} else {
					$event->uncancel();
				}

				if (KnockFFA::getInstance()->ingame == false) {
					$event->cancel();
				} else {
					$event->uncancel();
				}
				if ($player->getHealth() <= $event->getFinalDamage()) {
					$event->cancel();
					if (in_array($player->getName(), KnockFFA::getInstance()->lastdamager)) {
						if (is_string(KnockFFA::getInstance()->lastdamager[$player->getName()])) {
							$opfer = Server::getInstance()->getPlayerExact(KnockFFA::getInstance()->lastdamager[$player->getName()]);
							$event->cancel();

							if ($player->isOnline()) {
								$bwp = KnockFFA::getInstance()->getPlayerManager()->getPlayer($player->getName());
								$bwp->teleport();
							}
							Server::getInstance()->broadcastMessage(KnockFFA::PREFIX . "§a" . $player->getName() . " §cwas killed by §c" .
								$opfer->getName());
							BattleCore::getInstance()->statsSystem->addKill(KnockFFA::getInstance()->lastdamager[$player->getName()], "KnockFFA");
							BattleCore::getInstance()->statsSystem->addDeath($player, "KnockFFA");
							KnockFFA::getInstance()->lastdamager[$player->getName()] = false;

						} else {
							if (!$player->getGamemode() == GameMode::CREATIVE()) {

								Server::getInstance()->broadcastMessage("§a" . $player->getName() . " §7died!");

								BattleCore::getInstance()->statsSystem->addDeath($player, "KnockFFA");

								if ($player->isOnline()) {
									$bwp = KnockFFA::getInstance()->getPlayerManager()->getPlayer($player->getName());
									$bwp->teleport();
								}
								return;
							}
						}
					}
				} else if ($event->getCause() === EntityDamageEvent::CAUSE_VOID) {
					$event->cancel();
					if (true) { //spec
						if (KnockFFA::getInstance()->ingame == true) {
							if ($player->getGamemode() === GameMode::SPECTATOR()) {
								$event->cancel();
							} else {
								$bwPlayer->teleport();
								if (is_string(KnockFFA::getInstance()->lastdamager[$player->getName()])) {
									$opfer = Server::getInstance()->getPlayerExact(KnockFFA::getInstance()->lastdamager[$player->getName()]);
									Server::getInstance()->broadcastMessage("§a" . $player->getName() . " §cwas killed by §c" . $opfer->getName());
									BattleCore::getInstance()->statsSystem->addKill(KnockFFA::getInstance()->lastdamager[$player->getName()], "KnockFFA");
									BattleCore::getInstance()->statsSystem->addDeath($player, "KnockFFA");
									KnockFFA::getInstance()->lastdamager[$player->getName()] = false;
									KnockFFA::getInstance()->lastdamager[$opfer->getName()] = false;


								} else {
									Server::getInstance()->broadcastMessage("§c" . $player->getName() . "§c died" . "§c and in therefore eliminated!");
									BattleCore::getInstance()->statsSystem->addDeath($player, "KnockFFA");
								}
							}
						} else {

							$player->teleport(Server::getInstance()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
						}

					}
				}
			}
		}
	}

}