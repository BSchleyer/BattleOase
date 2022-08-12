<?php

namespace battleoase\battlecore\player\object;

use battleoase\battlecore\BattlePlayer;
use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;

class Scoreboard {

    public const OBJECTIVE_NAME = "scoreboard:battleoase";
    public const DISPLAY_NAME = "BattleOase";
    public const DISPLAY_SLOT = self::DISPLAY_SLOT_SIDEBAR;
    public const SORT_ORDER = self::SORT_ORDER_ASCENDING;

    public const DISPLAY_SLOT_LIST = "list";
    public const DISPLAY_SLOT_SIDEBAR = "sidebar";

    public const SORT_ORDER_ASCENDING = 0;
    public const SORT_ORDER_DESCENDING = 1;

    private array $lines = [];
    private bool $canSee = false;

    public function __construct(
        private BattlePlayer $player
    ){}

    public function setLine(int $score, string $line, bool $send = false): void{
        //$line = (($score <= 9 ? "§".$score : "§l§".$score."§".$score)." §r").$line;
        $this->lines[$score] = $line;

        if($send){
            $scoreEntry = new ScorePacketEntry();
            $scoreEntry->type = ScorePacketEntry::TYPE_FAKE_PLAYER;
            $scoreEntry->score = $score;
            $scoreEntry->objectiveName = self::OBJECTIVE_NAME;
            $scoreEntry->customName = $line;
            $scoreEntry->scoreboardId = $score;
            $setScorePacket = new SetScorePacket();
            $setScorePacket->type = SetScorePacket::TYPE_CHANGE;
            $setScorePacket->entries = [$scoreEntry];
            $this->player->getNetworkSession()->sendDataPacket($setScorePacket);
        }
    }

    public function removeLine(int $score, bool $send = false): void{
        unset($this->lines[$score]);
        if($send){
            $scoreEntry = new ScorePacketEntry();
            $scoreEntry->type = ScorePacketEntry::TYPE_FAKE_PLAYER;
            $scoreEntry->score = $score;
            $scoreEntry->objectiveName =  self::OBJECTIVE_NAME;
            $scoreEntry->scoreboardId = $score;
            $setScorePacket = new SetScorePacket();
            $setScorePacket->type = SetScorePacket::TYPE_REMOVE;
            $setScorePacket->entries = [$scoreEntry];
            $this->player->getNetworkSession()->sendDataPacket($setScorePacket);
        }
    }

    public function clearLines(): void{
        $this->lines = [];
    }

    public function getLine(int $score): ?string{
        return $this->lines[$score] ?? null;
    }

    public function send(): void{
        $player = $this->player;
        $this->remove();

        $setDisplayPacket = new SetDisplayObjectivePacket();
        $setDisplayPacket->criteriaName = "dummy";
        $setDisplayPacket->displayName = self::DISPLAY_NAME;
        $setDisplayPacket->objectiveName = self::OBJECTIVE_NAME;
        $setDisplayPacket->displaySlot = self::DISPLAY_SLOT;
        $setDisplayPacket->sortOrder = self::SORT_ORDER;
        $player->getNetworkSession()->sendDataPacket($setDisplayPacket);

        $entries = [];
        foreach($this->lines as $score => $line){
            $scoreEntry = new ScorePacketEntry();
            $scoreEntry->type = ScorePacketEntry::TYPE_FAKE_PLAYER;
            $scoreEntry->score = $score;
            $scoreEntry->objectiveName = self::OBJECTIVE_NAME;
            $scoreEntry->customName = $line;
            $scoreEntry->scoreboardId = $score;
            $entries[] = $scoreEntry;
        }
        $setScorePacket = new SetScorePacket();
        $setScorePacket->type = SetScorePacket::TYPE_CHANGE;
        $setScorePacket->entries = $entries;
        $player->getNetworkSession()->sendDataPacket($setScorePacket);
        $this->canSee = true;
    }

    public function remove(bool $clearLines = false): void{
        if(!$this->canSee) return;
        $this->canSee = false;

        if($clearLines){
            $this->clearLines();
        }

        $setScorePacket = new SetScorePacket();
        $setScorePacket->type = SetScorePacket::TYPE_REMOVE;
        $setScorePacket->entries = [];
        $removeObjectivePacket = new RemoveObjectivePacket();
        $removeObjectivePacket->objectiveName = self::OBJECTIVE_NAME;
        $this->player->getNetworkSession()->sendDataPacket($setScorePacket);
        $this->player->getNetworkSession()->sendDataPacket($removeObjectivePacket);
    }

}