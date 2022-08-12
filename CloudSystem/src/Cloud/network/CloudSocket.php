<?php

namespace Cloud\network;

use Cloud\Cloud;
use Cloud\network\protocol\handler\PacketHandler;
use Cloud\network\protocol\packet\ConnectionPacket;
use Cloud\network\protocol\packet\DispatchCommandPacket;
use Cloud\network\protocol\packet\InvalidPacket;
use Cloud\network\protocol\packet\ListServersRequestPacket;
use Cloud\network\protocol\packet\ListServersResponsePacket;
use Cloud\network\protocol\packet\LoginRequestPacket;
use Cloud\network\protocol\packet\LogPacket;
use Cloud\network\protocol\packet\NotifyStatusUpdatePacket;
use Cloud\network\protocol\packet\Packet;
use Cloud\network\protocol\packet\PlayerInfoRequestPacket;
use Cloud\network\protocol\packet\PlayerJoinPacket;
use Cloud\network\protocol\packet\PlayerKickPacket;
use Cloud\network\protocol\packet\PlayerQuitPacket;
use Cloud\network\protocol\packet\ProxyPlayerJoinPacket;
use Cloud\network\protocol\packet\ProxyPlayerQuitPacket;
use Cloud\network\protocol\packet\SaveServerPacket;
use Cloud\network\protocol\packet\ServerInfoRequestPacket;
use Cloud\network\protocol\packet\ServerInfoResponsePacket;
use Cloud\network\protocol\packet\StartServerRequestPacket;
use Cloud\network\protocol\packet\StartServerResponsePacket;
use Cloud\network\protocol\packet\StopServerRequestPacket;
use Cloud\network\protocol\packet\StopServerResponsePacket;
use Cloud\network\protocol\packet\TextPacket;
use Cloud\network\protocol\PacketPool;
use Cloud\network\udp\UDPClient;
use Cloud\network\udp\UDPServer;
use Cloud\network\utils\Address;
use Cloud\scheduler\ClosureTask;
use Cloud\server\Server;
use Cloud\server\ServerManager;
use Cloud\server\status\ServerStatus;
use Cloud\template\Template;
use Cloud\template\TemplateManager;
use Cloud\utils\CloudLogger;

class CloudSocket extends PacketHandler {

    private static self $instance;
    private UDPServer $udpServer;
    private PacketPool $packetPool;
    /** @var UDPClient[] */
    private array $clients = [];

    public function __construct(Address $bindAddress) {
        self::$instance = $this;
        $this->udpServer = new UDPServer();
        $this->packetPool = new PacketPool();

        CloudLogger::getInstance()->info("Binding to §e" . $bindAddress . "§r...");
        $this->udpServer->bind($bindAddress);
        CloudLogger::getInstance()->info("Successfully binded to §e" . $bindAddress . "§r!");

        Cloud::getInstance()->getTaskScheduler()->scheduleTask(new ClosureTask(function (): void {
            $this->onRun();
        }, 0, true, 1));
    }

    public function getUdpServer(): UDPServer {
        return $this->udpServer;
    }

    public function onRun() {
        if ($this->udpServer->isConnected()) {
            if (($this->udpServer->read($buffer, $address, $port)) !== false) {
                if ($this->isLocalHost($address)) {
                    $packet = $this->packetPool->getPacket($buffer);
                    if (!$packet instanceof InvalidPacket) {
                        $packet->decode();
                        $client = new UDPClient(new Address($address, $port));

                        if (!$this->isVerified($client)) {
                            if ($packet instanceof LoginRequestPacket) {
                                $this->handleLogin($packet, $client);
                            }
                        } else {
                            if ($packet instanceof ConnectionPacket) {
                                $this->handleConnection($packet, $client);
                            } else if ($packet instanceof DispatchCommandPacket) {
                                $this->handleDispatchCommand($packet, $client);
                            } else if ($packet instanceof SaveServerPacket) {
                                $this->handleSaveServer($packet, $client);
                            } else if ($packet instanceof NotifyStatusUpdatePacket) {
                                $this->handleNotifyStatusUpdate($packet, $client);
                            } else if ($packet instanceof PlayerJoinPacket) {
                                $this->handleJoin($packet, $client);
                            } else if ($packet instanceof PlayerQuitPacket) {
                                $this->handleQuit($packet, $client);
                            } else if ($packet instanceof ProxyPlayerJoinPacket) {
                                $this->handleProxyJoin($packet, $client);
                            } else if ($packet instanceof ProxyPlayerQuitPacket) {
                                $this->handleProxyQuit($packet, $client);
                            } else if ($packet instanceof LogPacket) {
                                $this->handleLog($packet, $client);
                            } else if ($packet instanceof TextPacket) {
                                $this->handleText($packet, $client);
                            } else if ($packet instanceof PlayerKickPacket) {
                                $this->handlePlayerKick($packet, $client);
                            } else if ($packet instanceof StartServerRequestPacket) {
                                $this->handleStartServer($packet, $client);
                            } else if ($packet instanceof StopServerRequestPacket) {
                                $this->handleStopServer($packet, $client);
                            } else if ($packet instanceof ListServersRequestPacket) {
                                $this->handleListServers($packet, $client);
                            } else if ($packet instanceof ServerInfoRequestPacket) {
                                $this->handleServerInfo($packet, $client);
                            } else if ($packet instanceof PlayerInfoRequestPacket) {
                                $this->handlePlayerInfo($packet, $client);
                            }
                        }
                    }
                } else {
                    CloudLogger::getInstance()->warning("Received a packet from a external client!");
                }
            }
        }
    }

    public function isVerified(UDPClient $client): bool {
        foreach ($this->clients as $server => $serverClient) {
            if ($client->getAddress()->equals($serverClient->getAddress())) return true;
        }
        return false;
    }

    public function isVerifiedServer(Server $server): bool {
        foreach ($this->clients as $serverName => $serverClient) {
            if ($serverName == $server->getName()) return true;
        }
        return false;
    }

    public function verify(UDPClient $client, string $server) {
        if (!isset($this->clients[$server])) $this->clients[$server] = $client;
    }

    public function unverify(string $server) {
        if (isset($this->clients[$server])) unset($this->clients[$server]);
    }

    private function isLocalHost(string $address): bool {
        return $address == "127.0.0.1" || $address == "0.0.0.0" || $address == "8.8.8.8" || $address == "localhost";
    }

    public function getClient(string $serverName): ?UDPClient {
        foreach ($this->clients as $server => $client) {
            if ($server == $serverName) return $client;
        }
        return null;
    }

    public function getServer(UDPClient $client): ?string {
        foreach ($this->clients as $server => $serverClient) {
            if ($client->getAddress()->equals($serverClient->getAddress())) return $server;
        }
        return null;
    }

    public function sendPacket(Packet $packet, UDPClient $client) {
        $client->sendPacket($packet);
    }

    public function broadcastPacket(Packet $packet) {
        foreach ($this->clients as $client) $client->sendPacket($packet);
    }

    public function getPacketPool(): PacketPool {
        return $this->packetPool;
    }

    /** @return UDPClient[] */
    public function getClients(): array {
        return $this->clients;
    }

    public static function getInstance(): CloudSocket {
        return self::$instance;
    }
}