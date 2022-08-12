<?php

namespace battleoase\battlecore\gameAPI;

use battleoase\battlecore\gameAPI\objects\Game;
use battleoase\battlecore\gameAPI\objects\State;
use battleoase\battlecore\utils\BPlugin;

class GameAPI extends BPlugin {

    /** @var array $games */
    public array $games = [];


    public function __construct() {

    }

    public function registerGame(Game $game) {
        $this->games[$game->getName()] = $game;
    }

    /**
     * @return Game[]
     */
    public function getGames(): array {
        return $this->games;
    }
    
}