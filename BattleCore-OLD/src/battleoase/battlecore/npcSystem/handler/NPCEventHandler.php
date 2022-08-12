<?php
namespace battleoase\battlecore\npcSystem\handler;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;

abstract class NPCEventHandler
{
	public abstract function onHit(Entity &$entity, EntityDamageByEntityEvent &$event): bool;

	// Space for other events

}