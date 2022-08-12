<?php

namespace Cloud\thread;

use Cloud\Cloud;

abstract class Thread extends \Thread {

    private bool $running = false;

    public function run() {
        spl_autoload_register(function (string $class): void {
            if (substr($class, 0, strlen("Cloud\\")) === "Cloud\\") {
                if (!class_exists($class)) require str_replace("\\", DIRECTORY_SEPARATOR, __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . substr($class, strlen("Cloud\\"), strlen($class))) . ".php";
            }
        });

        $this->running = true;
        $this->onRun();
    }

    abstract public function onRun();

    public function shutdown() {
        $this->running = false;
    }

    public function isRunning(): bool {
        return $this->running;
    }
}