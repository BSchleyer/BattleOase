<?php

namespace CloudBridge\network;

use CloudBridge\CloudBridge;
use CloudBridge\network\protocol\packet\InvalidPacket;
use CloudBridge\network\protocol\packet\Packet;
use CloudBridge\network\protocol\PacketPool;
use CloudBridge\network\udp\UDPClient;
use CloudBridge\network\utils\Address;
use pocketmine\player\Player;
use pocketmine\snooze\SleeperNotifier;
use pocketmine\thread\Thread;

class CloudBridgeSocket extends Thread {

    private static self $instance;
    private PacketPool $packetPool;
    private SleeperNotifier $sleeperNotifier;
    private \Threaded $buffer;
    private \Socket $socket;
    private bool $connected = false;
    private Address $address;

    public function __construct(Address $connectAddress, SleeperNotifier $sleeperNotifier, \Threaded $buffer) {
        self::$instance = $this;
        $this->address = $connectAddress;
        $this->sleeperNotifier = $sleeperNotifier;
        $this->buffer = $buffer;
    }

    public function onRun(): void {
        $this->registerClassLoaders();
        $this->packetPool = new PacketPool();

        while (true) {
            if ($this->isConnected()) {
                if (($read = $this->read($buffer, $address, $port)) !== false) {
                    $packet = $this->packetPool->getPacket($buffer);
                    if (!$packet instanceof InvalidPacket) {
                        $packet->decode();

                        $this->buffer[] = $packet;
                        $this->sleeperNotifier->wakeupSleeper();
                    }
                }
            }
        }
    }

    public function connect() {
        if ($this->connected) return;
        $this->socket = @socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        CloudBridge::getInstance()->getServer()->getLogger()->info("Connecting to §e" . $this->address . "§r...");
        if (@socket_connect($this->socket, $this->address->getAddress(), $this->address->getPort())) {
            $this->connected = true;
            CloudBridge::getInstance()->getServer()->getLogger()->info("Successfully connected to §e" . $this->address . "§r!");
            CloudBridge::getInstance()->getServer()->getLogger()->info("§cWait for incoming packets...");
        } else {
            $error = socket_last_error($this->socket);
            throw new \Exception("Failed to connect to $this->address: " . trim(socket_strerror($error)), $error);
        }
    }

    public function write(string $buffer): bool|int {
        return @socket_send($this->socket, $buffer, strlen($buffer), 0);
    }

    public function read(?string &$buffer, ?string &$address, ?int &$port): bool|int {
        return @socket_recvfrom($this->socket, $buffer, 65535, 0, $address, $port);
    }

    public function close() {
        if ($this->isConnected()) {
            $this->connected = false;
            @socket_close($this->socket);
        }
    }

    public function sendPacket(Packet $packet) {
        $packet->encode();
        $json = json_encode($packet->getPacketContent());
        if ($json !== false) {
            $this->write($json);
        }
    }

    public function getAddress(): Address {
        return $this->address;
    }

    public function getSocket(): \Socket {
        return $this->socket;
    }

    public function getPacketPool(): PacketPool {
        return $this->packetPool;
    }

    public function isConnected(): bool {
        return $this->connected;
    }

    public static function getInstance(): CloudBridgeSocket {
        return self::$instance;
    }
}