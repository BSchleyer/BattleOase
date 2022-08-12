<?php

namespace Cloud\console;

use Cloud\Cloud;
use Cloud\command\CommandManager;
use Cloud\lib\snooze\SleeperNotifier;
use Cloud\thread\Thread;

class CloudConsole extends Thread {

    private SleeperNotifier $sleeperNotifier;
    private \Threaded $buffer;

    public function __construct(\Threaded $buffer, SleeperNotifier $sleeperNotifier) {
        $this->buffer = $buffer;
        $this->sleeperNotifier = $sleeperNotifier;
    }

    public function onRun() {
        while ($this->isRunning()) {
            $stdin = fopen('php://stdin', 'r');
            $line = trim(fgets($stdin));
            fclose($stdin);

            if ($line !== "") {
                $this->buffer[] = $line;
                $this->sleeperNotifier->wakeupSleeper();
            }
        }
    }
}