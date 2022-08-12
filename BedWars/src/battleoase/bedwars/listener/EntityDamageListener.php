<?php

namespace battleoase\bedwars\listener;


use battleoase\battlecore\BattleCore;
use battleoase\bedwars\api\TeamAPI;
use battleoase\bedwars\BedWars;
use battleoase\bedwars\classes\Team;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\math\Vector3;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\scheduler\CancelTaskException;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class EntityDamageListener implements Listener
{
	public function onEntity(EntityDamageByEntityEvent $event)
	{
		$player = $event->getEntity();
		$damager = $event->getDamager();
		//
		//			$damagerTeam = BedWars::getInstance()->getPlayerManager()->getPlayer($damager->getName())->getTeam();
		//			$playerTeam = BedWars::getInstance()->getPlayerManager()->getPlayer($player->getName())->getTeam();

		if ($player instanceof Player) {
			if($damager instanceof Player) {
			$bwPlayer = BedWars::getInstance()->getPlayerManager()->getPlayer($player->getName());
			$bwDamager = BedWars::getInstance()->getPlayerManager()->getPlayer($damager->getName());
			if ($damager->getGamemode() === GameMode::SPECTATOR()) {
				//BedWars::getInstance()->players[$damager->getName()] = $player->getName();
				$damager->setGamemode(GameMode::SPECTATOR());
				$damager->hidePlayer($player);
				$damager->hidePlayer($damager);
			}

			if (BedWars::getInstance()->ingame == false) {
				$event->cancel();
			}
			if ($bwPlayer->getTeam()->getName() == $bwDamager->getTeam()->getName()) {
				$event->cancel();

			} else {
				BedWars::getInstance()->lastdamager[$player->getName()] = $damager->getName();
				if ($player->getGamemode() === GameMode::SURVIVAL()) {
					if ($player->getHealth() <= $event->getFinalDamage()) {
						$event->cancel();
						if ($bwPlayer->getTeam()->hasBed()) {
							Server::getInstance()->broadcastMessage("§a" . TeamAPI::getTeamColor($bwPlayer->getTeam()->getName()) . $player->getName() . " §cwas killed by §c" .
								$damager->getName());
						} else {
							Server::getInstance()->broadcastMessage("§a" . TeamAPI::getTeamColor($bwPlayer->getTeam()->getName()) . $player->getName() . "§c was killed by §c" .
								$damager->getName() . "§c and in therefore eliminated!");
							BedWars::getInstance()->getPlayerManager()->getPlayer($bwPlayer)->removeTeam();
						}
						$bwDamager->addKill();
						BattleCore::getInstance()->statsSystem->addKill($damager, "BedWars");
						BattleCore::getInstance()->statsSystem->addDeath($player, "BedWars");
						if ($bwPlayer->getTeam()->hasBed()) {
							$bwPlayer->teleport();
						} else {
							$player->setGamemode(GameMode::SPECTATOR());
							$bwPlayer->removeTeam();
						}

					}
				}
			}

			}
		} else {
			if ($damager instanceof Player) {
				BedWars::getInstance()->getPlayerManager()->getPlayer($damager->getName())->getShopMenu()->send($damager);
				$event->cancel();
			}
		}
	}

	public function onDamage(EntityDamageEvent $event)
	{
		$player = $event->getEntity();

		if ($player instanceof Player) {
			$bwPlayer = BedWars::getInstance()->getPlayerManager()->getPlayer($player->getName());
			if ($player->getGamemode() === GameMode::CREATIVE()) {
				$event->cancel();
			}
			if ($event->getCause() === EntityDamageEvent::CAUSE_FALL || $event->getCause() == EntityDamageEvent::CAUSE_BLOCK_EXPLOSION || $event->getCause() == EntityDamageEvent::CAUSE_ENTITY_EXPLOSION) {
				if (BedWars::getInstance()->saveDamager == true) {
					$event->cancel();
				} else {
					$event->uncancel();
				}

				if (BedWars::getInstance()->ingame == false) {
					$event->cancel();
				} else {
					$event->uncancel();
				}
				if ($player->getHealth() <= $event->getFinalDamage()) {
					$event->cancel();
					if (in_array($player->getName(), BedWars::getInstance()->lastdamager)) {
						if (is_string(BedWars::getInstance()->lastdamager[$player->getName()])) {
							$opfer = Server::getInstance()->getPlayerExact(BedWars::getInstance()->lastdamager[$player->getName()]);
							$bwOpfer = BedWars::getInstance()->getPlayerManager()->getPlayer($opfer->getName());
							$event->cancel();
							if ($bwPlayer->getTeam()->hasBed()) {
								BedWars::getInstance()->getScheduler()->scheduleDelayedTask(new class($player) extends Task {

									public function __construct(Player $player)
									{
										$this->player = $player;
									}

									public function onRun(): void
									{
										if ($this->player->isOnline()) {
											$bwp = BedWars::getInstance()->getPlayerManager()->getPlayer($this->player->getName());
											$bwp->teleport();
										}
									}


								}, 1);
							} else {
								$player->setGamemode(GameMode::SPECTATOR());
								BedWars::getInstance()->getPlayerManager()->getPlayer($player)->removeTeam();
							}

							if ($bwPlayer->getTeam()->hasBed()) {
								Server::getInstance()->broadcastMessage("§a" . TeamAPI::getTeamColor($bwPlayer->getTeam()->getName()) . $player->getName() . "§c was killed by §c" .
									TeamAPI::getTeamColor($bwPlayer->getTeam()) . $opfer->getName());
							} else {
								Server::getInstance()->broadcastMessage("§a" . TeamAPI::getTeamColor($bwPlayer->getTeam()->getName()) . $player->getName() . " §cwas killed by §c" .
									TeamAPI::getTeamColor($bwPlayer->getTeam()) . $opfer->getName() . "§c and in therefore eliminated!");
							}
							$bwOpfer->addKill();
							BattleCore::getInstance()->statsSystem->addKill(BedWars::getInstance()->lastdamager[$player->getName()], "BedWars");
							BattleCore::getInstance()->statsSystem->addDeath($player, "BedWars");
							BedWars::getInstance()->lastdamager[$player->getName()] = false;

						} else {
							if (!$player->getGamemode() == GameMode::CREATIVE()) {

								if ($bwPlayer->getTeam()->hasBed()) {
									Server::getInstance()->broadcastMessage("§a" . TeamAPI::getTeamColor($bwPlayer->getTeam()->getName()) . $player->getName() . " §7died!");
								} else {
									Server::getInstance()->broadcastMessage("§c" . TeamAPI::getTeamColor($bwPlayer->getTeam()->getName()) . $player->getName() . " §cdied" . "§c and in therefore eliminated!");
								}
								BattleCore::getInstance()->statsSystem->addDeath($player, "BedWars");
								if ($bwPlayer->getTeam()->hasBed()) {
									BedWars::getInstance()->getScheduler()->scheduleDelayedTask(new class($player) extends Task {

										public function __construct(Player $player)
										{
											$this->player = $player;
										}

										public function onRun(): void
										{
											if ($this->player->isOnline()) {
												$bwp = BedWars::getInstance()->getPlayerManager()->getPlayer($this->player->getName());
												$bwp->teleport();
											}
										}


									}, 1);
								} else {
									$player->setGamemode(GameMode::SPECTATOR());
									$bwPlayer->removeTeam();
								}
							}
							return;
						}
					}
				}
				} else if ($event->getCause() === EntityDamageEvent::CAUSE_VOID) {
					$event->cancel();
					if (true) { //spec
						if (BedWars::getInstance()->ingame == true) {
							if ($player->getGamemode() === GameMode::SPECTATOR()) {
								$event->cancel();
							} else {
								$bwPlayer->teleport();
								if (is_string(BedWars::getInstance()->lastdamager[$player->getName()])) {
									$opfer = Server::getInstance()->getPlayerExact(BedWars::getInstance()->lastdamager[$player->getName()]);
										$bwOpfer = BedWars::getInstance()->getPlayerManager()->getPlayer($opfer->getName());
										if ($bwPlayer->getTeam()->hasBed()) {

											Server::getInstance()->broadcastMessage("§a" . TeamAPI::getTeamColor($bwPlayer->getTeam()->getName()) . $player->getName() . " §cwas killed by §c" .
												TeamAPI::getTeamColor($bwOpfer->getTeam()->getName()) . $opfer->getName());
										} else {
											Server::getInstance()->broadcastMessage("§a" . TeamAPI::getTeamColor($bwPlayer->getTeam()->getName()) . $player->getName() . "§c was killed by §c" .
												TeamAPI::getTeamColor($bwOpfer->getTeam()->getName()) . $opfer->getName() . "§c and in therefore eliminated!");
										}
										$bwLastDamagerBedWars = BedWars::getInstance()->getPlayerManager()->getPlayer(BedWars::getInstance()->lastdamager[$player->getName()]);
										$bwLastDamagerBedWars->addKill();
										BattleCore::getInstance()->statsSystem->addKill(BedWars::getInstance()->lastdamager[$player->getName()], "BedWars");
										BattleCore::getInstance()->statsSystem->addDeath($player, "BedWars");
										BedWars::getInstance()->lastdamager[$player->getName()] = false;
										BedWars::getInstance()->lastdamager[$opfer->getName()] = false;


								} else {

									if ($bwPlayer->getTeam()->hasBed()) {
										Server::getInstance()->broadcastMessage("§a" . TeamAPI::getTeamColor($bwPlayer->getTeam()->getName()) . $player->getName() . "§7 died!");
									} else {
										Server::getInstance()->broadcastMessage("§c" . TeamAPI::getTeamColor($bwPlayer->getTeam()->getName()) . $player->getName() . "§c died" . "§c and in therefore eliminated!");
									}
									BattleCore::getInstance()->statsSystem->addDeath($player, "BedWars");
								}
								if ($bwPlayer->getTeam()->hasBed()) {
									$bwPlayer->teleport();
								} else {
									$player->setGamemode(GameMode::SPECTATOR());
									$bwPlayer->removeTeam();
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