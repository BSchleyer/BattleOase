<?php


namespace battleoase\bedwars\listener;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\BattlePlayer;
use battleoase\battlecore\customInteractSystem\events\PlayerInteractEventWithDelay;
use battleoase\bedwars\api\TeamAPI;
use battleoase\bedwars\BedWars;
use battleoase\bedwars\ui\MapVotingUi;
use ceepkev77\cloudbridge\CloudBridge;
use ceepkev77\cloudbridge\network\DataPacket;
use ceepkev77\cloudbridge\network\packet\GameServerInfoRequestPacket;
use ceepkev77\cloudbridge\network\packet\ListServerRequestPacket;
use ceepkev77\cloudbridge\network\packet\ListServerResponsePacket;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDropItemEvent;

class PlayerInteractListener implements Listener
{

	private array $gg = [];
	private array $ez = [];

	public function InteractEvent(PlayerInteractEventWithDelay $eventWithDelay)
	{
		$name = $eventWithDelay->getItem()->getCustomName();
		$player =  $eventWithDelay->getPlayer();
		switch ($name) {
			case "§8● §7Map Voting":
				//if(BedWars::getInstance()->countdown >= 10) {
					(new MapVotingUi($player))->open();
				//}
				break;
			case "§8● §4Leave":
				if ($player instanceof BattlePlayer){
					$player->kick("FALLBACK");
				}
				break;
            case "§8● §eHub":
                if ($player instanceof BattlePlayer){
                    $player->kick("FALLBACK");
                }
                break;
			case "§8● §7Select Team":
				break;
			case "§8● §7Gold Voting":
				break;
		}

	}

	public function chat(PlayerChatEvent $event) {
		$player = $event->getPlayer();
		if (BedWars::getInstance()->ingame == false){
			if(BedWars::getInstance()->saveDamager == true) {
				if(str_contains($event->getMessage(), "gg")) {
					if(!in_array($player->getName(), $this->gg)) {
						$colorarray = ["§c", "§a", "§b", "§d", "§5", "§6", "e"];
						$event->setMessage(str_replace("gg", "§a§l✔§r§f ".$colorarray[array_rand($colorarray)]."gg§r " . "§a§l✔§r§f", $event->getMessage()));

						$this->gg[] = $player->getName();
						if ($player instanceof BattlePlayer){
							$coins = 10;
							$player->sendMessage(BedWars::PREFIX . "§e+" . $coins . " §7Coins");
							$player->addCoins($coins);
						}
					}
				} elseif(str_contains($event->getMessage(), "ez")) {
					if(!in_array($event->getPlayer()->getName(), $this->ez)) {
						$this->ez[] = $event->getPlayer()->getName();
						if ($player instanceof BattlePlayer){
							$coins = 10;
							$player->sendMessage(BedWars::PREFIX . "§c-" . $coins . " §7Coins");
							$player->removeCoins($coins);
						}
					}
				}
			}
		}
	}

	public function onDrop(PlayerDropItemEvent $event){

		if (BedWars::getInstance()->ingame == false){
			$event->cancel();
		}else{
			$event->uncancel();
		}
	}

	public function onPick(EntityItemPickupEvent $event){

		if (BedWars::getInstance()->ingame == false){
			$event->cancel();
		}else{
			$event->uncancel();
		}
	}

}