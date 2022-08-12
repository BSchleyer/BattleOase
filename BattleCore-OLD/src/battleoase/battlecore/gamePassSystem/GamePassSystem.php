<?php

namespace battleoase\battlecore\gamePassSystem;

use battleoase\battlecore\gamePassSystem\command\GamePassCommand;
use battleoase\battlecore\gamePassSystem\provider\GamePassProvider;
use battleoase\battlecore\utils\BPlugin;

class GamePassSystem extends BPlugin {

    const TITLE = "§eGamePass";
    /** @var GamePassProvider $gamePassProvider */
    public GamePassProvider $gamePassProvider;


    public function __construct() {
        $this->gamePassProvider = new GamePassProvider();
        $this->getServer()->getCommandMap()->register("gamepass", new GamePassCommand());
    }

}