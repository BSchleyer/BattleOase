<?php

namespace battleoase\battlecore\gameAPI\objects;

use battleoase\battlecore\BattleCore;
use pocketmine\scheduler\Task;

class Game {

    public State $state;

    public function __construct(public string $name, public Task $task, public $team, public $maxPlayerInTeam) {
        $this->state = State::WAITING();
        BattleCore::getInstance()->getScheduler()->scheduleRepeatingTask($this->task, 20);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getMaxPlayerInTeam(): int
    {
        return $this->maxPlayerInTeam;
    }

    /**
     * @return int
     */
    public function getTeam(): int
    {
        return $this->team;
    }

    /**
     * @return State
     */
    public function getState(): State
    {
        return $this->state;
    }

    /**
     * @param State $state
     */
    public function setState(State $state): void
    {
        $this->state = $state;
    }



}