<?php

declare(strict_types=1);

namespace battleoase\battlecore\player\trait;

trait PlayerAfkTrait
{

    protected int $afkTicks = 0;

    public function isAfk(): bool {
        return $this->afkTicks >= self::AFK_TICKS;
    }

    public function getAfkTicks(): int {
        return $this->afkTicks;
    }

    public function resetAfkTicks(): void {
        $this->afkTicks = 0;
    }

}