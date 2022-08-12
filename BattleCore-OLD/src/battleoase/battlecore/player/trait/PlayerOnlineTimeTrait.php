<?php

namespace battleoase\battlecore\player\trait;

trait PlayerOnlineTimeTrait {
    private int $onlineTime = -1;
    private int $joinTime = 0;

    public function getOnlineTime(): int{
        return $this->onlineTime + (time() - $this->joinTime);
    }

    public function initOnlineTime(int $onlineTime): void{
        $this->onlineTime = $onlineTime;
        $this->joinTime = time();
    }
}