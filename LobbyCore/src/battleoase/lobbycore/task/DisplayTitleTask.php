<?php

namespace battleoase\lobbycore\task;

use pocketmine\player\Player;
use pocketmine\scheduler\Task;

class DisplayTitleTask extends Task {

    private Player $player;
    private int $count = 14;

    public function __construct(Player $player) {
        $this->player = $player;
    }

    public function onRun(): void {
        if ($this->count == 9) {
            $this->player->sendTitle("§8» §8_", "§fW§8_");
        } else if ($this->count == 8) {
            $this->player->sendTitle("§8» §3B§8_", "§fWi§8_");
        } else if ($this->count == 7) {
            $this->player->sendTitle("§8» §3Ba§8_", "§fWil§8_");
        } else if ($this->count == 6) {
            $this->player->sendTitle("§8» §3Bat§8_", "§fWill§8_");
        } else if ($this->count == 5) {
            $this->player->sendTitle("§8» §3Batt§8_", "§fWillk§8_");
        } else if ($this->count == 4) {
            $this->player->sendTitle("§8» §3Battle§8_", "§fWillko§8_");
        } else if ($this->count == 3) {
            $this->player->sendTitle("§8» §3Battle§bO§8_", "§fWillkom§8_");
        } else if ($this->count == 2) {
            $this->player->sendTitle("§8» §3Battle§bOa§8_", "§fWillkomm§8_");
        } else if ($this->count == 1) {
            $this->player->sendTitle("§8» §3Battle§bOas§8_ §8«", "§fWillkomme§8_");
        } else if ($this->count == 0) {
            $this->player->sendTitle("§8» §3Battle§bOase §8«", "§fWillkommen, §b" . $this->player->getName() . "§8!");
            $this->getHandler()->cancel();
        }
        $this->count--;
    }

}