<?php


namespace battleoase\battlecore\customInteractSystem;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\BattlePlayer;
use battleoase\battlecore\customInteractSystem\events\PlayerInteractEventWithDelay;
use battleoase\battlecore\customInteractSystem\tasks\ResetDelayTask;
use battleoase\battlecore\languageSystem\objects\Translation;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\lang\Translatable;

class CustomInteractListener implements Listener
{
	public function Interact(PlayerInteractEvent $event) {
        $player = $event->getPlayer();
        if($player instanceof BattlePlayer) {
            if(!$player->hasCooldown($event->getItem()->getName())) {
                $ev = new PlayerInteractEventWithDelay($event->getPlayer(), $event->getItem(), $event->getBlock(), $event->getTouchVector(), $event->getFace(), PlayerInteractEvent::RIGHT_CLICK_BLOCK);
                $ev->call();
                $player->resetCooldown($event->getItem()->getName(), 10);
            }
        }
	}
}