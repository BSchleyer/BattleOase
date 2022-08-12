<?php

namespace ceepkev77\cloudbridge\listener\cloud;

use pocketmine\event\Cancellable;
use pocketmine\event\player\PlayerEvent;

class ProxyPlayerQuitEvent extends PlayerEvent implements Cancellable
{


    private string $playerName;

    public function __construct($playerName)
    {
        $this->playerName = $playerName;
    }

    /**
     * @return string
     */
    public function getPlayerName(): string
    {
        return $this->playerName;
    }

    public function isCancelled(): bool
    {
        return false;
    }
}