<?php

namespace Cloud\network\protocol;

use Cloud\network\protocol\packet\ConnectionPacket;
use Cloud\network\protocol\packet\DisconnectPacket;
use Cloud\network\protocol\packet\DispatchCommandPacket;
use Cloud\network\protocol\packet\InvalidPacket;
use Cloud\network\protocol\packet\ListServersRequestPacket;
use Cloud\network\protocol\packet\ListServersResponsePacket;
use Cloud\network\protocol\packet\LoginRequestPacket;
use Cloud\network\protocol\packet\LoginResponsePacket;
use Cloud\network\protocol\packet\LogPacket;
use Cloud\network\protocol\packet\NotifyStatusUpdatePacket;
use Cloud\network\protocol\packet\Packet;
use Cloud\network\protocol\packet\PlayerInfoRequestPacket;
use Cloud\network\protocol\packet\PlayerInfoResponsePacket;
use Cloud\network\protocol\packet\PlayerJoinPacket;
use Cloud\network\protocol\packet\PlayerKickPacket;
use Cloud\network\protocol\packet\PlayerQuitPacket;
use Cloud\network\protocol\packet\ProxyPlayerJoinPacket;
use Cloud\network\protocol\packet\ProxyPlayerQuitPacket;
use Cloud\network\protocol\packet\RegisterServerPacket;
use Cloud\network\protocol\packet\SaveServerPacket;
use Cloud\network\protocol\packet\SendNotifyPacket;
use Cloud\network\protocol\packet\ServerInfoRequestPacket;
use Cloud\network\protocol\packet\ServerInfoResponsePacket;
use Cloud\network\protocol\packet\StartServerRequestPacket;
use Cloud\network\protocol\packet\StartServerResponsePacket;
use Cloud\network\protocol\packet\StopServerRequestPacket;
use Cloud\network\protocol\packet\StopServerResponsePacket;
use Cloud\network\protocol\packet\TextPacket;
use Cloud\network\protocol\packet\UnregisterServerPacket;

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
            new RegisterServerPacket(),
            new UnregisterServerPacket(),
            new NotifyStatusUpdatePacket(),
            new SendNotifyPacket(),
            new PlayerJoinPacket(),
            new PlayerQuitPacket(),
            new ProxyPlayerJoinPacket(),
            new ProxyPlayerQuitPacket(),
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