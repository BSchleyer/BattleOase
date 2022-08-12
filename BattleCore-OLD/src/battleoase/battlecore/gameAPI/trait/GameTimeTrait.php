<?php

namespace battleoase\battlecore\gameAPI\trait;

trait GameTimeTrait {

    public int $seconds = 0;

    public function getSeconds(): int{
        return $this->seconds;
    }

    public function resetSeconds(): void{
        $this->seconds = 0;
    }

}