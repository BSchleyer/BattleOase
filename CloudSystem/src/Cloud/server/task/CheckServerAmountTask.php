<?php

namespace Cloud\server\task;

use Cloud\scheduler\Task;
use Cloud\server\ServerManager;

class CheckServerAmountTask extends Task {

    public function __construct() {
        parent::__construct(0, true, 20);
    }

    public function onRun(int $tick): void {
        ServerManager::getInstance()->startDefaultServers();
    }
}