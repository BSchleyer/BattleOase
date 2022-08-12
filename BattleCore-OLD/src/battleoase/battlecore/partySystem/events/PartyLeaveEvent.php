<?php

namespace battleoase\battlecore\partySystem\events;

use pocketmine\event\Event;

class PartyLeaveEvent extends Event {

    protected $player;
    protected $partyName;

    public function __construct(string $player, string $partyName){
        $this->player = $player;
        $this->partyName = $partyName;
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
}