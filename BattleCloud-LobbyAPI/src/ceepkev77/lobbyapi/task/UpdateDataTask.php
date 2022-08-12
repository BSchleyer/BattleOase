<?php

namespace ceepkev77\lobbyapi\task;

use ceepkev77\cloudbridge\network\DataPacket;
use ceepkev77\cloudbridge\network\packet\GameServerInfoRequestPacket;
use ceepkev77\cloudbridge\network\packet\GameServerInfoResponsePacket;
use ceepkev77\cloudbridge\network\packet\ListServerRequestPacket;
use ceepkev77\cloudbridge\network\packet\ListServerResponsePacket;
use ceepkev77\cloudbridge\objects\CloudGroup;
use ceepkev77\cloudbridge\objects\GameServer;
use ceepkev77\lobbyapi\LobbyAPI;
use pocketmine\scheduler\Task;

class UpdateDataTask extends Task
{

    public function onRun(): void
    {

        $pk2 = new ListServerRequestPacket();
        $pk2->submitRequest($pk2, function (DataPacket $dataPacketList) {
            if($dataPacketList instanceof ListServerResponsePacket) {
                $servers = json_decode($dataPacketList->data["servers"], true);
                foreach (LobbyAPI::getGameServerProvider()->getGameServers() as $gameServer) {
                    if ($gameServer instanceof GameServer) {
                        if (!in_array($gameServer->getName(), $servers)) {
                            var_dump($gameServer->getName() ." local unregistered");
                            LobbyAPI::getGameServerProvider()->unregisterServer($gameServer);
                        }
                    }
                }
                foreach ($servers as $server) {
                    $serverInfoPacket = new GameServerInfoRequestPacket();
                    $serverInfoPacket->server = $server;
                    $serverInfoPacket->submitRequest($serverInfoPacket, function (DataPacket $dataPacket) {
                        if($dataPacket instanceof GameServerInfoResponsePacket) {
                            if(LobbyAPI::getGameServerProvider()->getGameServer($dataPacket->getServerInfoName()) != null) {
                                LobbyAPI::getGameServerProvider()->getGameServer($dataPacket->getServerInfoName())->setState($dataPacket->getState());
                                LobbyAPI::getGameServerProvider()->getGameServer($dataPacket->getServerInfoName())->setIsPrivate($dataPacket->isPrivate());
                                LobbyAPI::getGameServerProvider()->getGameServer($dataPacket->getServerInfoName())->setPlayerCount($dataPacket->getPlayerCount());
                            } else {
                                $gameServer = new GameServer($dataPacket->getServerInfoName(), new CloudGroup($dataPacket->getTemplateName(), $dataPacket->isMaintenance(), $dataPacket->isBeta(), $dataPacket->isLobby(), $dataPacket->getMaxPlayer()));
                                $gameServer->setState($dataPacket->getState());
                                $gameServer->setIsPrivate($dataPacket->isPrivate());
                                $gameServer->setPlayerCount($dataPacket->getPlayerCount());
                                LobbyAPI::getGameServerProvider()->registerGameServer($gameServer);
                            }

                        }
                    });
                }
            }
        });
    }

}