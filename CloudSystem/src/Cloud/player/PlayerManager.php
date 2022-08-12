<?php

namespace Cloud\player;

use Cloud\server\Server;
use Cloud\server\ServerManager;

class PlayerManager {

    private static PlayerManager $instance;
    /** @var CloudPlayer[] */
    private array $players = [];
    private array $lastProxy = [];

    public function __construct() {
        self::$instance = $this;
    }

    public function handleLogin(CloudPlayer $player) {
        if (!isset($this->players[$player->getName()])) $this->players[$player->getName()] = $player;
    }

    public function handleLogout(CloudPlayer $player) {
        $players = $this->players;
        if (array_key_exists($player->getName(), $players)) {
            unset($players[$player->getName()]);
            $this->players = $players;
            array_push($this->players);
        }

        $this->setLastProxy($player, $player->getCurrentProxy());
    }

    public function addServerPlayer(CloudPlayer $player) {
        $server = ServerManager::getInstance()->getServer($player->getCurrentServer());
        if ($server !== null) $server->addPlayer($player);
    }

    public function removeServerPlayer(CloudPlayer $player) {
        $server = ServerManager::getInstance()->getServer($player->getCurrentServer());
        if ($server !== null) $server->removePlayer($player);
    }

    public function addProxyPlayer(CloudPlayer $player) {
        $server = ServerManager::getInstance()->getServer($player->getCurrentProxy());
        if ($server !== null) $server->addPlayer($player);
    }

    public function removeProxyPlayer(CloudPlayer $player) {
        $server = ServerManager::getInstance()->getServer($player->getCurrentProxy());
        if ($server !== null) $server->removePlayer($player);
    }

    public function setLastProxy(CloudPlayer|string $player, string $proxy) {
        $name = $player instanceof CloudPlayer ? $player->getName() : $player;
        $this->lastProxy[$name] = $proxy;
    }

    public function removeLastProxy(CloudPlayer|string $player) {
        $name = $player instanceof CloudPlayer ? $player->getName() : $player;
        if ($this->hasLastProxy($name)) unset($this->lastProxy[$name]);
    }

    public function hasLastProxy(CloudPlayer|string $player): bool {
        $name = $player instanceof CloudPlayer ? $player->getName() : $player;
        return isset($this->lastProxy[$name]);
    }

    public function getLastProxy(CloudPlayer|string $player): ?string {
        $playerName = $player instanceof CloudPlayer ? $player->getName() : $player;
        foreach ($this->lastProxy as $name => $proxy) {
            if ($name == $playerName) return $proxy;
        }
        return null;
    }

    public function getPlayer(string $name): ?CloudPlayer {
        foreach ($this->players as $player) {
            if ($player->getName() == $name) return $player;
        }
        return null;
    }

    public function getPlayers(): array {
        return $this->players;
    }

    public static function getInstance(): PlayerManager {
        return self::$instance;
    }
}
