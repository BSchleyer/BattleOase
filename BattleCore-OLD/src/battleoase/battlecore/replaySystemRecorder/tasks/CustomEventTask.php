<?php


namespace battleoase\battlecore\replaySystemRecorder\tasks;

use battleoase\battlecore\replaySystemRecorder\events\EntityMoveEvent;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class CustomEventTask extends Task {


	public function onRun(): void {
		foreach (Server::getInstance()->getWorldManager()->getWorlds() as $level) {
			foreach ($level->getEntities() as $entity) {
				$newVector = $entity->getPosition()->asVector3();
				//$oldVector = new Vector3();
				//replace the argument #1 to $oldvector
				$event = new EntityMoveEvent($entity, $newVector, $newVector);
				$event->call();
				if($event->isCancelled()){

					//replace the argument #0 to $oldvector
					$entity->teleport($newVector);
				}
			}
		}
	}
}