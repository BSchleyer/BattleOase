<?php

namespace battleoase\bedwars\listener;

use battleoase\battlecore\BattleCore;
use battleoase\bedwars\api\TeamAPI;
use battleoase\bedwars\BedWars;
use battleoase\bedwars\caches\TeamCache;
use ceepkev77\cloudbridge\CloudBridge;
use pocketmine\block\tile\Bed;
use pocketmine\data\bedrock\DyeColorIdMap;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\math\Vector3;
use pocketmine\player\GameMode;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\world\sound\FireExtinguishSound;
use pocketmine\world\sound\GhastShootSound;
use xxFLORII\Cores\Main;

class BlockBreakListener implements Listener
{

    public function onBlockBreak(BlockBreakEvent $event)
    {
        $tile = $event->getBlock()->getPosition()->getWorld()->getTile($event->getBlock()->getPosition());
        if (BedWars::getInstance()->getPlayerManager()->getPlayer($event->getPlayer())->getBuildMode() === true){
        	$event->uncancel();
		}else{
			if ($tile instanceof Bed) {
				$color = $tile->getColor();
				$teambed = TeamAPI::ColorIntToTeam(DyeColorIdMap::getInstance()->toId($color));
				$bwplayer = BedWars::getInstance()->getPlayerManager()->getPlayer($event->getPlayer());

				if ($teambed === $bwplayer->getTeam()->getName()) {
					$event->getPlayer()->sendMessage(BedWars::PREFIX . BattleCore::getInstance()->getLanguageSystem()->translate($event->getPlayer(), "BedWars.break.YourBed"));
					$event->cancel();
				} else {

					if (TeamCache::get($teambed)->bed) {
						foreach (Server::getInstance()->getOnlinePlayers() as $player){
							$player->getWorld()->addSound(new Vector3($player->getPosition()->getX(), $player->getPosition()->getY(), $player->getPosition()->getZ()), new GhastShootSound());

							$player->sendMessage(BedWars::PREFIX . BattleCore::translate($player, "bedwars.message.breakBed", [
									"{TEAM}" => TeamCache::get($teambed)->getDisplayName(),
									"{PLAYER}" => $event->getPlayer()->getNameTag()
								]));

						}

						foreach (TeamCache::get($teambed)->getPlayers() as $player) {
							$pl = Server::getInstance()->getPlayerExact($player);
							//$pl->sendTitle("§cBed Destroyed\n§7Your Bed was destroyed");
							$pl->sendTitle(" ", "§r" . str_repeat("\n", 6) . str_repeat(" ", 36) . "§cBed Destroyed\n§7Your Bed was destroyed", 1, 18, 1);
						}

						TeamCache::get($teambed)->setBed(false);
					} else {
						$event->getPlayer()->sendMessage(BedWars::PREFIX . BattleCore::getInstance()->getLanguageSystem()->translate($event->getPlayer(), "bedwars.already.break"));
						$event->cancel();
					}
					$event->setDrops([]);
				}
			} elseif (!in_array($event->getBlock()->getId(), BedWars::BLOCKS)) {
				$event->cancel();
			}
		}

    }

}