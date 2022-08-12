<?php

namespace battleoase\battlecore\gameAPI\trait;

use battleoase\battlecore\gameAPI\objects\Game;

trait AutofillTrait {

    protected Game $game;

    /**
     * @return Game
     */
    public function getGame(): Game {
        return $this->game;
    }

    /**
     * @param Game $game
     */
    public function setGame(Game $game): void {
        $this->game = $game;
    }

}