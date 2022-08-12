<?php

namespace xxFLORII\Cores\Listener;

use battleoase\battlecore\BattleCore;
use battleoase\battlecore\BattlePlayer;
use battleoase\battlecore\pluginPlayer\player\BPlayer;
use xxFLORII\Cores\API\CoresAPI;
use xxFLORII\Cores\Main;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\Server;

class DamageListener implements Listener {

    public function onDamage(EntityDamageEvent $event)
    {

        $player = $event->getEntity();
        $config = Main::getInstance()->getConfig();
        if ($config->get("ingame") === false) {

            $event->cancel();

        } else {

            if ($event instanceof EntityDamageByEntityEvent) {
                $damager = $event->getDamager();
                if ($damager instanceof Player && $player instanceof Player) {

                    if ($damager->isSpectator()){
                        $event->cancel();
                        return;
                    }

                    if (in_array($damager->getName(), Main::$redTeam) && in_array($player->getName(), Main::$redTeam)){
                        $event->cancel();
                    } elseif (in_array($damager->getName(), Main::$blueTeam) && in_array($player->getName(), Main::$blueTeam)){
                        $event->cancel();
                    } else {
                        $event->uncancel();
                    }
                }
            }
            if ($event->getCause() === $event::CAUSE_VOID){


				if ($player instanceof Player) {

					BattleCore::getInstance()->getLanguageSystem()->translate($player, "message.youDied");
					foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer){
						BattleCore::getInstance()->getLanguageSystem()->translate($onlinePlayer, "message.playerDied", [
							"{PLAYER}" => $player->getDisplayName()
						]);
					}

					$event->cancel();

					$player->setHealth(20);
					$player->getHungerManager()->setFood(20);
					(new CoresAPI())->teleportIngame($player);
					(new CoresAPI())->giveKit($player);
				}
            } elseif ($event instanceof EntityDamageByEntityEvent){
                $damager = $event->getDamager();

                if ($damager instanceof BattlePlayer && $player instanceof BattlePlayer) {
                    if ($player->getHealth() <= $event->getFinalDamage()){
						BattleCore::getInstance()->getLanguageSystem()->translate($player, "message.youKilledBy", [
							"{DAMAGER}" => $damager->getDisplayName()
						]);

						BattleCore::getInstance()->getLanguageSystem()->translate($damager, "message.youKilled", [
							"{PLAYER}" => $player->getDisplayName()
						]);

                        BattleCore::getInstance()->statsSystem->addKill($damager, "Cores");
						BattleCore::getInstance()->statsSystem->addDeath($player, "Cores");

						$damager->addCoins(mt_rand(10, 25));

                        foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer){
							BattleCore::getInstance()->getLanguageSystem()->translate($onlinePlayer, "message.playerKilledBy", [
								"{DAMAGER}" => $damager->getDisplayName(),
								"{PLAYER}" => $damager->getDisplayName()
							]);
                        }

                        $event->cancel();

                        $player->setHealth(20);
                        $player->getHungerManager()->setFood(20);
						(new CoresAPI())->teleportIngame($player);
						(new CoresAPI())->giveKit($player);
                    }
                }
            }else {
				if ($player->getHealth() <= $event->getFinalDamage()){
					$event->cancel();

					$player->setHealth(20);
					$player->getHungerManager()->setFood(20);
					(new CoresAPI())->teleportIngame($player);
					(new CoresAPI())->giveKit($player);
				}
			}
        }
    }
}
