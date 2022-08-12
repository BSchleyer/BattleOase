<?php

namespace battleoase\lobbycore\feature;

use pocketmine\event\Listener;

abstract class Feature implements Listener {
    public string $name;

    public function getName(): string{
        return $this->name;
    }

    public function onLoad(): void {}
    public function onUnload(): void {}

    public function onUpdate(): bool {
        return false;
    }

    public function scheduleUpdate(): void {
        FeatureManager::getInstance()->scheduleUpdate($this);
    }
}