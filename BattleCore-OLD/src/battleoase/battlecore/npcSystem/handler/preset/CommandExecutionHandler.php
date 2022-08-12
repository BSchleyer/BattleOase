<?php

namespace battleoase\battlecore\npcSystem\handler\preset;

use battleoase\battlecore\npcSystem\handler\NPCEventHandler;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\network\mcpe\protocol\types\recipe\FurnaceRecipeBlockName;
use pocketmine\player\Player;
use pocketmine\Server;

class CommandExecutionHandler extends NPCEventHandler
{
	private $command;


	public function onHit(Entity &$entity, EntityDamageByEntityEvent &$event): bool
	{
		$player = $event->getDamager();
		if ($player instanceof Player) {
		    if($this->getCommand() !== "") {
                Server::getInstance()->dispatchCommand($player, $this->getCommand());
            }

		}
		return false;
	}

	/**
	 * @return mixed
	 */
	public function getCommand()
	{
		return $this->command;
	}

	/**
	 * @param mixed $command
	 */
	public function setCommand($command): void
	{
		$this->command = $command;
	}

	public function getName(): string {
	    return "command";
    }
}