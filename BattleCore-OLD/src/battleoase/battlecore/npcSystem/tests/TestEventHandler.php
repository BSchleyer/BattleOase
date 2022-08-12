<?php

namespace battleoase\battlecore\npcSystem\tests;

use battleoase\battlecore\npcSystem\handler\NPCEventHandler;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;

class TestEventHandler extends NPCEventHandler
{
	public function onHit(Entity &$entity, EntityDamageByEntityEvent &$event): bool
	{
		$event->getDamager()->sendMessage("lol");
		return false;
	}
}