<?php

namespace battleoase\battlecore\replaySystemPlayer\entities;

use pocketmine\entity\Human;

class HumanEntity extends Human implements IReplayEntity {

    /** @var int  */
    public $gravity = 0;
    /** @var int  */
    public $drag = 0;
    /** @var bool  */
    public bool $needsUpdate = false;
    /** @var int  */
    private int $nextSkinUpdate = 0;

    /**
     * @param int $currentTick
     * @return bool
     */

    public function onUpdate(int $currentTick) : bool {
        if($currentTick >= $this->nextSkinUpdate) {
            //$this->sendSkin();
            $this->nextSkinUpdate = $currentTick + 20 * 90;
        }
        if(!$this->needsUpdate) {
            return false;
        }
        $this->needsUpdate = false;
        $this->getWorld()->loadChunk($this->getPosition()->x, $this->getPosition()->z);
        $this->setHealth(20);
        return parent::onUpdate($currentTick);
    }
}