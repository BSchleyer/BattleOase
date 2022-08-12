<?php

namespace battleoase\battlecore\player\trait;

use pocketmine\Server;

trait PlayerCooldownTrait
{

    public array $cooldown = [];

    public function hasCooldown(string $cooldown): bool
    {
        $this->checkCooldowns();
        return isset($this->cooldown[$cooldown]);
    }

    public function checkCooldowns(): void
    {
        $severTicks = Server::getInstance()->getTick();
        foreach ($this->cooldown as $key => $cooldownUntil) {
            if($cooldownUntil <= $severTicks) unset($this->cooldown[$key]);
        }
    }

    public function resetCooldown(string $cooldown, int $ticks): void
    {
        $this->cooldown[$cooldown] = Server::getInstance()->getTick() + $ticks;
    }

}