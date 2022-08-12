<?php

namespace battleoase\lobbycore\feature\boots\boots;

use battleoase\battlecore\BattlePlayer;
use pocketmine\entity\Location;
use pocketmine\nbt\tag\StringTag;

abstract class Boots {

    abstract public function getName(): string;
    abstract public function getPrice(): int;

    protected function use(BattlePlayer $player): void {}

    public function isAllowedAtLocation(Location $location): bool {
        return false;
    }

}