<?php

namespace battleoase\battlecore\partySystem\events;

use pocketmine\event\Cancellable;
use pocketmine\event\Event;
use pocketmine\player\Player;

class PartyJoinEvent extends Event implements Cancellable{

    protected string $player;
    protected string $partyName;
    protected mixed $isCancelled;

    public function __construct(string $player, string $partyName, $isCancelled=false){
        $this->player = $player;
        $this->partyName = $partyName;
        $this->isCancelled = $isCancelled;
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

    public function cancel(): void
    {
        $this->isCancelled = true;
    }

    public function uncancel(): void
    {
        $this->isCancelled = false;
    }

    public function isCancelled(): bool
    {
        return $this->isCancelled;
    }
}