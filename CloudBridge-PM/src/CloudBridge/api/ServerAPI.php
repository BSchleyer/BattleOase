<?php

namespace CloudBridge\api;

use CloudBridge\network\CloudBridgeSocket;
use CloudBridge\network\protocol\packet\ListServersRequestPacket;
use CloudBridge\network\protocol\packet\PlayerInfoRequestPacket;
use CloudBridge\network\protocol\packet\ServerInfoRequestPacket;
use CloudBridge\network\protocol\packet\StartServerRequestPacket;
use CloudBridge\network\protocol\packet\StopServerRequestPacket;
use pocketmine\player\Player;

class ServerAPI {

    public static function startServer(Player $player, string $template, int $count = 1) {
        CloudBridgeSocket::getInstance()->sendPacket(StartServerRequestPacket::create($player->getName(), $template, $count));
    }

    public static function stopServer(Player $player, string $server) {
        CloudBridgeSocket::getInstance()->sendPacket(StopServerRequestPacket::create($player->getName(), $server));
    }

    public static function listServers(Player $player) {
        CloudBridgeSocket::getInstance()->sendPacket(ListServersRequestPacket::create($player->getName()));
    }

    public static function serverInfo(Player $player, string $server) {
        CloudBridgeSocket::getInstance()->sendPacket(ServerInfoRequestPacket::create($server, $player->getName()));
    }

    public static function playerInfo(Player $player, string $target) {
        CloudBridgeSocket::getInstance()->sendPacket(PlayerInfoRequestPacket::create($target, $player->getName()));
    }
}