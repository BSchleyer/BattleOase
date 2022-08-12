<?php

namespace Cloud\event;

abstract class Event {

    public function getName(): string {
        return (new \ReflectionClass($this))->getShortName();
    }

    public function call() {
        EventManager::getInstance()->callEvent($this);
    }
}