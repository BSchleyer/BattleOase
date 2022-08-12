<?php

namespace Cloud\network\protocol\handler;

use Cloud\api\NotifyAPI;
use Cloud\network\CloudSocket;
use Cloud\network\protocol\packet\ConnectionPacket;
use Cloud\network\protocol\packet\DispatchCommandPacket;
use Cloud\network\protocol\packet\ListServersRequestPacket;
use Cloud\network\protocol\packet\ListServersResponsePacket;
use Cloud\network\protocol\packet\LoginRequestPacket;
use Cloud\network\protocol\packet\LoginResponsePacket;
use Cloud\network\protocol\packet\LogPacket;
use Cloud\network\protocol\packet\NotifyStatusUpdatePacket;
use Cloud\network\protocol\packet\PlayerInfoRequestPacket;
use Cloud\network\protocol\packet\PlayerInfoResponsePacket;
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
use Cloud\network\udp\UDPClient;
use Cloud\network\utils\Address;
use Cloud\player\CloudPlayer;
use Cloud\player\PlayerManager;
use Cloud\server\ServerManager;
use Cloud\server\status\ServerStatus;
use Cloud\template\Template;
use Cloud\template\TemplateManager;
use Cloud\utils\CloudLogger;

class PacketHandler {

    public function handleLogin(LoginRequestPacket $packet, UDPClient $client) {
        if (($checkServer = ServerManager::getInstance()->getServer($packet->server)) !== null) {
            CloudSocket::getInstance()->verify($client, $checkServer->getName());
            CloudLogger::getInstance()->info("The server §e" . $checkServer->getName() . " §rwas §averified§r!");
            CloudSocket::getInstance()->sendPacket(LoginResponsePacket::create(LoginResponsePacket::SUCCESS), $client);
        } else {
            CloudLogger::getInstance()->warning("Received a login request from a not existing server! §8(§e" . $packet->server . "§8)");
            CloudSocket::getInstance()->sendPacket(LoginResponsePacket::create(LoginResponsePacket::DENIED), $client);
        }
    }

    public function handleConnection(ConnectionPacket $packet, UDPClient $client) {
        if (($server = ServerManager::getInstance()->getServer($packet->server)) !== null) {
            $server->setGotConnectionResponse(true);
        }
    }

    public function handleDispatchCommand(DispatchCommandPacket $packet, UDPClient $client) {
        if (($server = ServerManager::getInstance()->getServer($packet->server)) !== null) {
            ServerManager::getInstance()->dispatchCommand($server, $packet->commandLine);
        }
    }

    public function handleSaveServer(SaveServerPacket $packet, UDPClient $client) {
        if (($server = ServerManager::getInstance()->getServer($packet->server)) !== null) {
            ServerManager::getInstance()->saveServer($server);
        }
    }

    public function handleNotifyStatusUpdate(NotifyStatusUpdatePacket $packet, UDPClient $client) {
        NotifyAPI::getInstance()->setNotify($packet->player, $packet->v);
    }

    public function handleJoin(PlayerJoinPacket $packet, UDPClient $client) {
        if (($player = PlayerManager::getInstance()->getPlayer($packet->name)) !== null) {
            if ($player->getCurrentServer() == "") $player->setCurrentServer($packet->currentServer);
            PlayerManager::getInstance()->addServerPlayer($player);
        } else {
            $player = new CloudPlayer($packet->name, new Address($packet->address, $packet->port), $packet->uuid, $packet->xuid, $packet->currentServer, (PlayerManager::getInstance()->hasLastProxy($packet->name) ? PlayerManager::getInstance()->getLastProxy($packet->name) : ""));
            PlayerManager::getInstance()->handleLogin($player);
            PlayerManager::getInstance()->addServerPlayer($player);
            if ($player->getCurrentProxy() !== "") PlayerManager::getInstance()->addProxyPlayer($player);
        }
    }

    public function handleQuit(PlayerQuitPacket $packet, UDPClient $client) {
        if (($player = PlayerManager::getInstance()->getPlayer($packet->name)) !== null) {
            PlayerManager::getInstance()->handleLogout($player);
            PlayerManager::getInstance()->removeServerPlayer($player);
            $player->setCurrentServer("");
        }
    }

    public function handleProxyJoin(ProxyPlayerJoinPacket $packet, UDPClient $client) {
        $player = new CloudPlayer($packet->name, new Address($packet->address, $packet->port), $packet->uuid, $packet->xuid, "", $packet->currentProxy);
        PlayerManager::getInstance()->handleLogin($player);
        PlayerManager::getInstance()->addProxyPlayer($player);
    }

    public function handleProxyQuit(ProxyPlayerQuitPacket $packet, UDPClient $client) {
        if (($player = PlayerManager::getInstance()->getPlayer($packet->name)) !== null) {
            PlayerManager::getInstance()->handleLogout($player);
            PlayerManager::getInstance()->removeLastProxy($player);
            PlayerManager::getInstance()->removeProxyPlayer($player);
            $player->setCurrentProxy("");
        }
    }

    public function handleLog(LogPacket $packet, UDPClient $client) {
        $serverName = CloudSocket::getInstance()->getServer($client);
        if ($serverName !== null) CloudLogger::getInstance()->message("§e" . $serverName . ": §r" . $packet->message);
    }

    public function handleText(TextPacket $packet, UDPClient $client) {
        CloudSocket::getInstance()->broadcastPacket($packet);
    }
    
    public function handlePlayerKick(PlayerKickPacket $packet, UDPClient $client) {
        CloudSocket::getInstance()->broadcastPacket($packet);
    }
    
    public function handleStartServer(StartServerRequestPacket $packet, UDPClient $client) {
        if (($template = TemplateManager::getInstance()->getTemplate($packet->template)) !== null) {
            if (count(ServerManager::getInstance()->getServersOfTemplate($template)) >= $template->getMaxServers()) {
                $message = "§cNo servers from the template §e" . $template->getName() . " §ccan be started anymore because the limit was reached!";
                CloudSocket::getInstance()->sendPacket(StartServerResponsePacket::create($packet->player, $message, StartServerResponsePacket::ERROR), $client);
            } else {
                CloudSocket::getInstance()->sendPacket(StartServerResponsePacket::create($packet->player, "", StartServerResponsePacket::SUCCESS), $client);
                ServerManager::getInstance()->startServer($template, $packet->count);
            }
        } else {
            $message = "§cThe template §e" . $packet->template . " §cdoesn't exists!";
            CloudSocket::getInstance()->sendPacket(StartServerResponsePacket::create($packet->player, $message, StartServerResponsePacket::ERROR), $client);
        }
    }
    
    public function handleStopServer(StopServerRequestPacket $packet, UDPClient $client) {
        if ($packet->server == "all" || $packet->server == "*") {
            CloudSocket::getInstance()->sendPacket(StopServerResponsePacket::create($packet->player, "", StopServerResponsePacket::SUCCESS), $client);
        } else {
            if (($template = TemplateManager::getInstance()->getTemplate($packet->server)) !== null) {
                CloudSocket::getInstance()->sendPacket(StopServerResponsePacket::create($packet->player, "", StopServerResponsePacket::SUCCESS), $client);
                ServerManager::getInstance()->stopTemplate($template);
            } else if (($server = ServerManager::getInstance()->getServer($packet->server)) !== null) {
                CloudSocket::getInstance()->sendPacket(StopServerResponsePacket::create($packet->player, "", StopServerResponsePacket::SUCCESS), $client);
                ServerManager::getInstance()->stopServer($server);
            } else {
                $message = "§cThe server §e" . $packet->server . " §cdoesn't exists!";
                CloudSocket::getInstance()->sendPacket(StopServerResponsePacket::create($packet->player, $message, StopServerResponsePacket::ERROR), $client);
            }
        }
    }
    
    public function handleListServers(ListServersRequestPacket $packet, UDPClient $client) {
        $servers = [];
        foreach (ServerManager::getInstance()->getServers() as $server) $servers[$server->getName()] = ["Port" => $server->getPort(), "Players" => $server->getPlayersCount(), "MaxPlayers" => $server->getTemplate()->getMaxPlayers(), "Template" => ($server->getTemplate()->getType() == Template::TYPE_SERVER ? "§e" . $server->getTemplate()->getName() : "§c" . $server->getTemplate()->getName()), "ServerStatus" => $this->statusString($server->getServerStatus())];
        CloudSocket::getInstance()->sendPacket(ListServersResponsePacket::create($packet->player, $servers), $client);
    }
    
    public function handleServerInfo(ServerInfoRequestPacket $packet, UDPClient $client) {
        if (($server = ServerManager::getInstance()->getServer($packet->server)) !== null) {
            $players = [];
            foreach ($server->getPlayers() as $player) $players[] = $player->getName();
            CloudSocket::getInstance()->sendPacket(ServerInfoResponsePacket::create($server->getName(), $server->getId(), $server->getTemplate()->getName(), $server->getPort(), $players, $server->getTemplate()->getMaxPlayers(), $server->getServerStatus(), $packet->player, ServerInfoResponsePacket::SUCCESS), $client);
        } else {
            CloudSocket::getInstance()->sendPacket(ServerInfoResponsePacket::create($packet->server, 0, "", 0, [], 0, -1, $packet->player, ServerInfoResponsePacket::ERROR), $client);
        }
    }

    public function handlePlayerInfo(PlayerInfoRequestPacket $packet, UDPClient $client) {
        if (($player = PlayerManager::getInstance()->getPlayer($packet->target)) !== null) {
            CloudSocket::getInstance()->sendPacket(PlayerInfoResponsePacket::create($player->getName(), $player->getAddress()->getAddress(), $player->getAddress()->getPort(), $player->getUuid(), $player->getXuid(), $player->getCurrentServer(), $player->getCurrentProxy(), $packet->player, PlayerInfoResponsePacket::SUCCESS), $client);
        } else {
            CloudSocket::getInstance()->sendPacket(PlayerInfoResponsePacket::create($packet->target, "", 0, "", "", "", "", $packet->player, PlayerInfoResponsePacket::ERROR), $client);
        }
    }

    private function statusString(int $status): string {
        if ($status == ServerStatus::STATUS_STARTING) return "§2STARTING";
        else if ($status == ServerStatus::STATUS_STARTED) return "§aSTARTED";
        else if ($status == ServerStatus::STATUS_STOPPING) return "§4STOPPING";
        else if ($status == ServerStatus::STATUS_STOPPED) return "§cSTOPPED";
        return "";
    }
}