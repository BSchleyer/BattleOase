<?php


namespace battleoase\bedwars\listener;


use battleoase\battlecore\BattlePlayer;
use battleoase\bedwars\BedWars;
use pocketmine\entity\object\ItemEntity;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\Listener;
use pocketmine\inventory\PlayerInventory;
use pocketmine\player\Player;

class InventoryPickupItemListener implements Listener
{
	public function onPlayerTakeItem(EntityItemPickupEvent $event)
	{
		$owner = $event->getOrigin();
		$inv = $event->getInventory();
		$item = $event->getItem();

		$player = $event->getEntity();

		if ($player->fallDistance > 0.0) {
			$event->cancel();
		}

		if (!$player instanceof BattlePlayer) {
			return;
		}


		if (BedWars::getInstance()->ingame == true) {

			foreach (BedWars::getInstance()->stack as $itemStack) {
				if ($owner instanceof ItemEntity) {
					if ($itemStack->getPositionAsString() == $owner->getOwner()) {
						if (!$itemStack->getItem()->isClosed() && $player->getInventory()->canAddItem($itemStack->getItem()->getItem())) {
							if ($itemStack->getCount() >= 1) {
								$player->sendTip("§a+ {$itemStack->getCount()}");
								$player->getInventory()->addItem($itemStack->getItem()->getItem()->setCount($itemStack->getCount()));
								$player->playSound("random.pop", 0.250, 2.1);
							}
							$event->cancel();
						}
						if ($itemStack->getCount() >= 1) {
							$itemStack->setCount(0);
							$itemStack->getItem()->setOwner($itemStack->getItem()->getItem());
							$itemStack->getItem()->close();
						}
					}
				}

			}
		}
	}

}