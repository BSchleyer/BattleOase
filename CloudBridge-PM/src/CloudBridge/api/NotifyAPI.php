<?php

namespace CloudBridge\api;

use CloudBridge\CloudBridge;
use CloudBridge\network\CloudBridgeSocket;
use CloudBridge\network\protocol\packet\NotifyStatusUpdatePacket;
use pocketmine\player\Player;
use pocketmine\utils\Config;

class NotifyAPI {

    public static function isInNotifyMode(Player|string $player): bool {
        $name = $player instanceof Player ? $player->getName() : $player;
        return (self::getNotifyConfig()->exists($name) ? self::getNotifyConfig()->get($name) : false);
    }

    public static function setNotify(Player|string $player, bool $v) {
        $name = $player instanceof Player ? $player->getName() : $player;
        CloudBridgeSocket::getInstance()->sendPacket(NotifyStatusUpdatePacket::create($name, $v));
    }

    public static function getNotifyConfig(): Config {
        return new Config(CloudBridge::getInstance()->getCloudPath() . "local/notify/players.json", 1);
    }
}