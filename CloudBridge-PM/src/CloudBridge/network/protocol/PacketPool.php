<?php

namespace CloudBridge\network\protocol;

use CloudBridge\network\protocol\packet\ConnectionPacket;
use CloudBridge\network\protocol\packet\DisconnectPacket;
use CloudBridge\network\protocol\packet\DispatchCommandPacket;
use CloudBridge\network\protocol\packet\InvalidPacket;
use CloudBridge\network\protocol\packet\ListServersRequestPacket;
use CloudBridge\network\protocol\packet\ListServersResponsePacket;
use CloudBridge\network\protocol\packet\LoginRequestPacket;
use CloudBridge\network\protocol\packet\LoginResponsePacket;
use CloudBridge\network\protocol\packet\LogPacket;
use CloudBridge\network\protocol\packet\NotifyStatusUpdatePacket;
use CloudBridge\network\protocol\packet\Packet;
use CloudBridge\network\protocol\packet\PlayerInfoRequestPacket;
use CloudBridge\network\protocol\packet\PlayerInfoResponsePacket;
use CloudBridge\network\protocol\packet\PlayerJoinPacket;
use CloudBridge\network\protocol\packet\PlayerKickPacket;
use CloudBridge\network\protocol\packet\PlayerQuitPacket;
use CloudBridge\network\protocol\packet\SaveServerPacket;
use CloudBridge\network\protocol\packet\SendNotifyPacket;
use CloudBridge\network\protocol\packet\ServerInfoRequestPacket;
use CloudBridge\network\protocol\packet\ServerInfoResponsePacket;
use CloudBridge\network\protocol\packet\StartServerRequestPacket;
use CloudBridge\network\protocol\packet\StartServerResponsePacket;
use CloudBridge\network\protocol\packet\StopServerRequestPacket;
use CloudBridge\network\protocol\packet\StopServerResponsePacket;
use CloudBridge\network\protocol\packet\TextPacket;

class PacketPool {

    private static self $instance;
    private \SplFixedArray $packets;

    public function __construct() {
        self::$instance = $this;
        $this->packets = new \SplFixedArray(255);
        $this->registerPackets(
            new LoginRequestPacket(),
            new LoginResponsePacket(),
            new DisconnectPacket(),
            new ConnectionPacket(),
            new DispatchCommandPacket(),
            new SaveServerPacket(),
            new NotifyStatusUpdatePacket(),
            new SendNotifyPacket(),
            new PlayerJoinPacket(),
            new PlayerQuitPacket(),
            new LogPacket(),
            new TextPacket(),
            new PlayerKickPacket(),
            new StartServerRequestPacket(),
            new StartServerResponsePacket(),
            new StopServerRequestPacket(),
            new StopServerResponsePacket(),
            new ListServersRequestPacket(),
            new ListServersResponsePacket(),
            new ServerInfoRequestPacket(),
            new ServerInfoResponsePacket(),
            new PlayerInfoRequestPacket(),
            new PlayerInfoResponsePacket()
        );
    }

    public function registerPacket(Packet $packet) {
        $this->packets[$packet->getId()] = clone $packet;
    }

    public function registerPackets(Packet... $packets) {
        foreach ($packets as $packet) {
            $this->registerPacket($packet);
        }
    }

    public function getPacketById(int $id): ?Packet {
        return isset($this->packets[$id]) ? clone $this->packets[$id] : null;
    }

    public function getPacket(string $buffer): Packet {
        $contents = json_decode($buffer, true);
        if (is_array($contents)) {
            if (isset($contents[0])) {
                if (is_numeric($contents[0])) {
                    $packetId = intval($contents[0]);

                    $packet = self::getPacketById($packetId);
                    if ($packet !== null) {
                        $packet->setPacketContent($contents);
                        return $packet;
                    }
                }
            }
        }
        return new InvalidPacket();
    }

    public function getPackets(): \SplFixedArray {
        return $this->packets;
    }

    public static function getInstance(): PacketPool {
        return self::$instance;
    }
}