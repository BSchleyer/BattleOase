<?php

namespace ceepkev77\cloudbridge\objects;

class GameServer
{

    private string $name;
    private CloudGroup $cloudGroup;
    private int $state;
    public bool $isPrivate;
    private int $playerCount;

    public function __construct(String $name, CloudGroup $cloudGroup)
    {
        $this->name = $name;
        $this->cloudGroup = $cloudGroup;
        $this->state = GameServerState::NOT_REGISTERED;
        $this->isPrivate = false;
        $this->playerCount = 0;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCloudGroup(): CloudGroup
    {
        return $this->cloudGroup;
    }

    public function getState(): int
    {
        return $this->state;
    }

    public function setState(int $state): void
    {
        $this->state = $state;
    }

    public function setIsPrivate(bool $isPrivate): void
    {
        $this->isPrivate = $isPrivate;
    }

    public function isPrivate(): bool
    {
        return $this->isPrivate;
    }


    public function setIsMaintenance(bool $isMaintenance): void
    {
        $this->isMaintenance = $isMaintenance;
    }

    /**
     * @return int
     */
    public function getPlayerCount(): int
    {
        return $this->playerCount;
    }

    /**
     * @param int $playerCount
     */
    public function setPlayerCount(int $playerCount): void
    {
        $this->playerCount = $playerCount;
    }


}