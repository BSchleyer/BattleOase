<?php


namespace battleoase\lobbycore\events;


use battleoase\bedwars\BedWars;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\Listener;
use pocketmine\inventory\PlayerInventory;
use pocketmine\player\Player;

class InventoryPickupItemListener implements Listener
{
	public function pick(EntityItemPickupEvent $event) {
		$owner = $event->getOrigin();
		$inv = $event->getInventory();
		$itemEntity = $event->getItem();

		if (!$inv instanceof PlayerInventory) {
			return;
		}

		$player = $inv->getHolder();
		if($player instanceof Player) {
			foreach (BedWars::getInstance()->stack as $itemStack) {
				if ($itemStack->getPositionAsString() == $owner) {
					$event->cancel();
					if ($player->getInventory()->canAddItem($itemStack->getItem()->getItem())) {
						$player->getInventory()->addItem($itemStack->getItem()->getItem()->setCount($itemStack->getCount()));
					}
					$itemStack->setCount(0);
				}
			}
		}
	}

}