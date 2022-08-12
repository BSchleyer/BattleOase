<?php

namespace ceepkev77\cloudbridge\network\handler;

use ceepkev77\cloudbridge\CloudBridge;
use ceepkev77\cloudbridge\listener\cloud\CloudPacketReceiveEvent;
use ceepkev77\cloudbridge\network\DataPacket;
use ceepkev77\cloudbridge\network\RequestPacket;

class PacketHandler
{

    private static $registeredPackets = [];

    /**
     * @param string $packetName
     * @return bool
     */
    public static function isRegistered(string $packetName): bool
    {
        return isset(self::$registeredPackets[$packetName]);
    }

    /**
     * @param string $packetName
     * @param string $packet
     */
    public static function registerPacket(string $packetName, string $packet) {
        self::$registeredPackets[$packetName] = $packet;

    }

    /**
     * @param string $packetName
     * @return DataPacket
     */
    public static function getPacketClassByName(string $packetName): ?DataPacket {
        if(self::isRegistered($packetName)) return new self::$registeredPackets[$packetName];

        return null;
    }

    /**
     * @param string $packetName
     */
    public static function unregisterPacket(string $packetName) {
        unset(self::$registeredPackets[$packetName]);
    }

    public static function handleCloudPacket(string $packetBuffer)
    {
        $data = json_decode($packetBuffer, true);
        if(empty($data["packetName"]) || !self::isRegistered($data["packetName"])) return;
        $packet = self::getPacketClassByName($data["packetName"]);
        if($packet instanceof DataPacket) {
            $packet->data = $data;

            $packet->handle();
            if($packet instanceof RequestPacket) {
                if(isset($packet->data["requestId"]) && isset($packet->data["type"])) {
                    if($packet->data["type"] == DataPacket::TYPE_RESPONSE) {
                        $closure = CloudBridge::$requests[$packet->data["requestId"]] ?? null;
                        if($closure !== null) ($closure)($packet);
                        unset(CloudBridge::$requests[$packet->data["requestId"]]);
                    }
                }

            }
            $event = new CloudPacketReceiveEvent($packet);
            $event->call();
        }
    }


}