<?php

namespace CloudBridge\listener;

use CloudBridge\api\NotifyAPI;
use CloudBridge\CloudBridge;
use CloudBridge\event\PacketReceiveEvent;
use CloudBridge\network\CloudBridgeSocket;
use CloudBridge\network\protocol\packet\ConnectionPacket;
use CloudBridge\network\protocol\packet\DisconnectPacket;
use CloudBridge\network\protocol\packet\DispatchCommandPacket;
use CloudBridge\network\protocol\packet\ListServersResponsePacket;
use CloudBridge\network\protocol\packet\LoginResponsePacket;
use CloudBridge\network\protocol\packet\LogPacket;
use CloudBridge\network\protocol\packet\PlayerInfoResponsePacket;
use CloudBridge\network\protocol\packet\PlayerJoinPacket;
use CloudBridge\network\protocol\packet\PlayerKickPacket;
use CloudBridge\network\protocol\packet\PlayerQuitPacket;
use CloudBridge\network\protocol\packet\SendNotifyPacket;
use CloudBridge\network\protocol\packet\ServerInfoResponsePacket;
use CloudBridge\network\protocol\packet\StartServerResponsePacket;
use CloudBridge\network\protocol\packet\StopServerResponsePacket;
use CloudBridge\network\protocol\packet\TextPacket;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\XboxLivePlayerInfo;
use pocketmine\Server;

class PacketListener implements Listener {

    public function onLogin(PlayerPreLoginEvent $event) {
        $info = $event->getPlayerInfo();
        $xuid = ($info instanceof XboxLivePlayerInfo ? $info->getXuid() : "");
        CloudBridgeSocket::getInstance()->sendPacket(PlayerJoinPacket::create($info->getUsername(), $info->getUuid()->toString(), $xuid, $event->getIp(), $event->getPort(), CloudBridge::getInstance()->getServerName()));
    }

    public function onQuit(PlayerQuitEvent $event) {
        CloudBridgeSocket::getInstance()->sendPacket(PlayerQuitPacket::create($event->getPlayer()->getName()));
    }

    public function onReceive(PacketReceiveEvent $event) {
        $packet = $event->getPacket();
        $isInvalid = $event->isInvalid();

        if (!$isInvalid) {
            if ($packet instanceof LoginResponsePacket) {
                if ($packet->responseCode == $packet::SUCCESS) {
                    CloudBridge::getInstance()->getServer()->getLogger()->info("Server was §averified§r!");
                } else {
                    CloudBridge::getInstance()->getServer()->getLogger()->error("Server can't be verified!");
                    CloudBridge::getInstance()->getServer()->shutdown();
                }
            } else if ($packet instanceof ConnectionPacket) {
                CloudBridgeSocket::getInstance()->sendPacket(ConnectionPacket::create(CloudBridge::getInstance()->getServerName()));
            } else if ($packet instanceof DispatchCommandPacket) {
                if ($packet->server == CloudBridge::getInstance()->getServerName()) {
                    Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), $packet->commandLine);
                }
            } else if ($packet instanceof DisconnectPacket) {
                if ($packet->code == $packet::SERVER_SHUTDOWN) Server::getInstance()->shutdown();
                else if ($packet->code == $packet::CLOUD_SHUTDOWN) {
                    CloudBridge::getInstance()->getServer()->getLogger()->info("Cloud was stopped, shutdown the server...");
                    Server::getInstance()->shutdown();
                }
            } else if ($packet instanceof SendNotifyPacket) {
                foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                    if ($player->hasPermission("notify.receive")) {
                        if (NotifyAPI::isInNotifyMode($player)) {
                            $player->sendMessage(CloudBridge::getPrefix() . $packet->message);
                        }
                    }
                }
            } else if ($packet instanceof LogPacket) {
                CloudBridge::getInstance()->getServer()->getLogger()->info("§bCloud: §r" . $packet->message);
            } else if ($packet instanceof TextPacket) {
                if (($player = Server::getInstance()->getPlayerByPrefix($packet->player)) !== null) {
                    if ($packet->type == $packet::TYPE_MESSAGE) $player->sendMessage($packet->message);
                    else if ($packet->type == $packet::TYPE_TITLE) $player->sendTitle($packet->message);
                    else if ($packet->type == $packet::TYPE_POPUP) $player->sendPopup($packet->message);
                    else if ($packet->type == $packet::TYPE_TIP) $player->sendTip($packet->message);
                    else if ($packet->type == $packet::TYPE_ACTIONBAR) $player->sendActionBarMessage($packet->message);
                }
            } else if ($packet instanceof PlayerKickPacket) {
                if (($player = Server::getInstance()->getPlayerByPrefix($packet->player)) !== null) {
                    $player->kick($packet->reason);
                }
            } else if ($packet instanceof StartServerResponsePacket) {
                if (($player = Server::getInstance()->getPlayerByPrefix($packet->player)) !== null) {
                    if ($packet->code == $packet::SUCCESS) {
                        if ($packet->message !== "") $player->sendMessage(CloudBridge::getPrefix() . $packet->message);
                    } else {
                        $player->sendMessage(CloudBridge::getPrefix() . $packet->message);
                    }
                }
            } else if ($packet instanceof StopServerResponsePacket) {
                if (($player = Server::getInstance()->getPlayerByPrefix($packet->player)) !== null) {
                    if ($packet->code == $packet::SUCCESS) {
                        if ($packet->message !== "") $player->sendMessage(CloudBridge::getPrefix() . $packet->message);
                    } else {
                        $player->sendMessage(CloudBridge::getPrefix() . $packet->message);
                    }
                }
            } else if ($packet instanceof ListServersResponsePacket) {
                if (($player = Server::getInstance()->getPlayerByPrefix($packet->player)) !== null) {
                    $player->sendMessage(CloudBridge::getPrefix() . "Servers: §8(§e" . count($packet->servers) . "§8)");
                    foreach ($packet->servers as $server => $serverData) {
                        $player->sendMessage(CloudBridge::getPrefix() . "§e" . $server .
                            " §8| §7Port: §e" . $serverData["Port"] .
                            " §8| §7Players: §e" . $serverData["Players"] .
                            " §8| §7MaxPlayers: §e" . $serverData["MaxPlayers"] .
                            " §8| §7Template: §e" . $serverData["Template"] .
                            " §8| §7ServerStatus: §e" . $serverData["ServerStatus"]
                        );
                    }
                }
            } else if ($packet instanceof ServerInfoResponsePacket) {
                if (($player = Server::getInstance()->getPlayerByPrefix($packet->player)) !== null) {
                    if ($packet->code == $packet::SUCCESS) {
                        $player->sendMessage(CloudBridge::getPrefix() . "Name: §e" . $packet->name);
                        $player->sendMessage(CloudBridge::getPrefix() . "Id: §e" . $packet->id);
                        $player->sendMessage(CloudBridge::getPrefix() . "Template: §e" . $packet->template);
                        $player->sendMessage(CloudBridge::getPrefix() . "Port: §e" . $packet->port);
                        $player->sendMessage(CloudBridge::getPrefix() . "Players §8(§e" . count($packet->players) . "§8)§7: §e" . implode(", ", $packet->players));
                        $player->sendMessage(CloudBridge::getPrefix() . "MaxPlayers: §e" . $packet->maxPlayers);
                        $player->sendMessage(CloudBridge::getPrefix() . "ServerStatus: §e" . $this->statusString($packet->serverStatus));
                    } else {
                        $player->sendMessage(CloudBridge::getPrefix() . "§cThe server §e" . $packet->name . " §cdoesn't exists!");
                    }
                }
            } else if ($packet instanceof PlayerInfoResponsePacket) {
                if (($player = Server::getInstance()->getPlayerByPrefix($packet->player)) !== null) {
                    if ($packet->code == $packet::SUCCESS) {
                        $player->sendMessage(CloudBridge::getPrefix() . "Name: §e" . $packet->name);
                        $player->sendMessage(CloudBridge::getPrefix() . "Host: §e" . $packet->address . ":" . $packet->port);
                        $player->sendMessage(CloudBridge::getPrefix() . "UUID: §e" . $packet->uuid);
                        $player->sendMessage(CloudBridge::getPrefix() . "XUID: §e" . $packet->xuid);
                        $player->sendMessage(CloudBridge::getPrefix() . "CurrentServer: §e" . $packet->currentServer);
                        $player->sendMessage(CloudBridge::getPrefix() . "CurrentProxy: §c" . ($packet->currentProxy == "" ? "-" : $packet->currentProxy));
                    } else {
                        $player->sendMessage(CloudBridge::getPrefix() . "§cThe player §e" . $packet->name . " §cisn't online!");
                    }
                }
            }
        }
    }

    private function statusString(int $status): string {
        if ($status == 0) return "§2STARTING";
        else if ($status == 1) return "§aSTARTED";
        else if ($status == 2) return "§4STOPPING";
        else if ($status == 3 ) return "§cSTOPPED";
        return "";
    }
}