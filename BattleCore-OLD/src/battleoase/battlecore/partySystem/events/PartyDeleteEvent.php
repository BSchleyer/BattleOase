<?php

namespace battleoase\battlecore\partySystem\events;

use pocketmine\event\Cancellable;
use pocketmine\event\Event;
use pocketmine\player\Player;

class PartyDeleteEvent extends Event {

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