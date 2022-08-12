<?php

namespace battleoase\battlecore\customInteractSystem\tasks;


use battleoase\battlecore\BattleCore;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;

class ResetDelayTask extends Task
{
    public string $name;

    public function __construct($player)
    {
        $this->name = $player;
    }

    public function onRun(): void
    {
        $name = strtolower($this->name);
        if (in_array($this->name, BattleCore::getInstance()->customInteractSystem->delay)) {
            unset(BattleCore::getInstance()->customInteractSystem->delay[array_search($this->name, BattleCore::getInstance()->customInteractSystem->delay)]);
        }
    }
}
