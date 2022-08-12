<?php

namespace ceepkev77\cloudbridge\objects;

class CloudGroup
{


    public function __construct(protected String $name,protected bool $maintenance, protected bool $beta, protected bool $isLobby, protected int $maxPlayer) {

    }

    public function getIsLobby(): bool
    {
        return $this->isLobby;
    }

    public function getIsPrivate(): bool
    {
        return $this->isPrivate;
    }

    public function setIsLobby(bool $isLobby): void
    {
        $this->isLobby = $isLobby;
    }

    public function setIsPrivate(bool $isPrivate): void
    {
        $this->isPrivate = $isPrivate;
    }

    public function isMaintenance(): bool
    {
        return $this->maintenance;
    }

    public function isBeta(): bool
    {
        return $this->beta;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getMaxPlayer(): int
    {
        return $this->maxPlayer;
    }


}