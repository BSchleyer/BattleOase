<?php

namespace battleoase\battlecore\replaySystemRecorder\events;

use pocketmine\entity\Entity;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\entity\EntityEvent;
use pocketmine\math\Vector3;

class EntityMoveEvent extends EntityEvent implements Cancellable {

	use CancellableTrait;

    /** @var Vector3  */
    private $from;

    /** @var Vector3  */
    private $to;

    /**
     * EntityMoveEvent constructor.
     * @param Entity $entity
     * @param Vector3 $from
     * @param Vector3 $to
     */

    public function __construct(Entity $entity, Vector3 $from, Vector3 $to){
        $this->entity = $entity;
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @return Entity
     */

    public function getEntity() : Entity {
        return $this->entity;
    }

    /**
     * @return Vector3
     */

    public function getFrom() : Vector3 {
        return $this->from;
    }

    public function getTo() : Vector3 {
        return $this->to;
    }
}