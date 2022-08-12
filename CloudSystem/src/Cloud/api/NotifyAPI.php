<?php

namespace Cloud\api;

use Cloud\network\CloudSocket;
use Cloud\network\protocol\packet\SendNotifyPacket;
use Cloud\utils\Config;

class NotifyAPI {

    private static self $instance;

    public function __construct() {
        self::$instance = $this;
    }

    public function setNotify(string $player, bool $v) {
        $cfg = $this->getNotifyConfig();
        $cfg->set($player, $v);
        $cfg->save();
    }

    public function isInNotifyMode(string $player): bool {
        return ($this->getNotifyConfig()->exists($player) ? $this->getNotifyConfig()->get($player) : false);
    }

    public function getNotifyConfig(): Config {
        return new Config(CLOUD_PATH . "local/notify/players.json", 1);
    }

    public function sendNotify(string $message) {
        CloudSocket::getInstance()->broadcastPacket(SendNotifyPacket::create($message));
    }

    public static function getInstance(): NotifyAPI {
        return self::$instance;
    }
}