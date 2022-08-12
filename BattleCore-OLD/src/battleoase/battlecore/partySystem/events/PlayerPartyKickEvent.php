<?php

namespace battleoase\battlecore\partySystem\events;

use pocketmine\event\Cancellable;
use pocketmine\event\Event;
use pocketmine\player\Player;

class PlayerPartyKickEvent extends Event {

    protected $player;
    protected $partyName;
    protected $kickedBy;

    public function __construct(string $player, string $partyName, string $kickedBy){
        $this->player = $player;
        $this->partyName = $partyName;
        $this->kickedBy = $kickedBy;
    }

    /**
     * @return string
     */
    public function getPlayer(): string
    {
        return $this->player;
    }

    /**
     * @return string
     */
    public function getPartyName(): string
    {
        return $this->partyName;
    }

    /**
     * @return string
     */
    public function getKickedBy(): string
    {
        return $this->kickedBy;
    }
}