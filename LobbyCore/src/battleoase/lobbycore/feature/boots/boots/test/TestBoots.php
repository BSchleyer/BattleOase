<?php

namespace battleoase\lobbycore\feature\boots\boots\test;

use battleoase\battlecore\BattlePlayer;
use battleoase\lobbycore\feature\boots\boots\Boots;

class TestBoots extends Boots
{
    public function getName(): string {
        return "TestBoots";
    }

    public function getPrice(): int {
        return 10000;
    }

    protected function use(BattlePlayer $player): void {
        $player->sendMessage("use " . $this->getName());
    }

}