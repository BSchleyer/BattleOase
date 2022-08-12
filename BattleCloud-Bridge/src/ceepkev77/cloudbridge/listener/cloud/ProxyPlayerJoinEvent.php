<?php

namespace ceepkev77\cloudbridge\listener\cloud;

use pocketmine\event\Cancellable;
use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

class ProxyPlayerJoinEvent extends PlayerEvent implements Cancellable
{


    private string $playerName;

    public function __construct($playerName, Player $player)
    {
        $this->playerName = $playerName;
        $this->player = $player;
    }

    /**
     * @return string
     */
    public function getPlayerName(): string
    {
        return $this->playerName;
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function isCancelled(): bool
    {
        return false;
    }
}