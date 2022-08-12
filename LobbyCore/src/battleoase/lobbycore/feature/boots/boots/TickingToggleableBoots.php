<?php

namespace battleoase\lobbycore\feature\boots\boots;

use battleoase\battlecore\BattlePlayer;
use pocketmine\player\Player;

abstract class TickingToggleableBoots extends Boots
{

    /**
     * @param BattlePlayer $player
     */
    public function onUpdate(Player $player): bool
    {
        $this->use($player);
        return true;
    }

}