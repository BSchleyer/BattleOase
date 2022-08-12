<?php

namespace ceepkev77\lobbyapi;

use ceepkev77\cloudbridge\network\DataPacket;
use ceepkev77\cloudbridge\network\packet\GameServerInfoRequestPacket;
use ceepkev77\cloudbridge\network\packet\GameServerInfoResponsePacket;
use ceepkev77\cloudbridge\network\packet\ListServerRequestPacket;
use ceepkev77\cloudbridge\network\packet\ListServerResponsePacket;
use ceepkev77\cloudbridge\objects\CloudGroup;
use ceepkev77\cloudbridge\objects\GameServer;
use ceepkev77\lobbyapi\command\QuickJoinCommand;

use ceepkev77\lobbyapi\listener\ProxyPlayerJoinListener;
use ceepkev77\lobbyapi\provider\GameServerProvider;
use ceepkev77\lobbyapi\task\UpdateDataTask;
use pocketmine\plugin\PluginBase;

class LobbyAPI extends PluginBase
{


    public static GameServerProvider $gameServerProvider;

    protected function onEnable(): void
    {
        self::$gameServerProvider = new GameServerProvider();
        $pk2 = new ListServerRequestPacket();
        $pk2->submitRequest($pk2, function (DataPacket $dataPacket) {
            if($dataPacket instanceof ListServerResponsePacket) {
                $servers = json_decode($dataPacket->data["servers"], true);
                foreach ($servers as $server) {
                    $serverInfoPacket = new GameServerInfoRequestPacket();
                    $serverInfoPacket->server = $server;
                    $serverInfoPacket->submitRequest($serverInfoPacket, function (DataPacket $dataPacket) {
                        if($dataPacket instanceof GameServerInfoResponsePacket) {
                            $gameServer = new GameServer($dataPacket->getServerInfoName(), new CloudGroup($dataPacket->getTemplateName(), $dataPacket->isMaintenance(), $dataPacket->isBeta(), $dataPacket->isLobby(), $dataPacket->getMaxPlayer()));
                            $gameServer->setState($dataPacket->getState());
                            $gameServer->setIsPrivate($dataPacket->isPrivate());
                            $gameServer->setPlayerCount($dataPacket->getPlayerCount());
                            LobbyAPI::getGameServerProvider()->registerGameServer($gameServer);
                        }
                    });
                }
            }
        });
        $this->getScheduler()->scheduleRepeatingTask(new UpdateDataTask(), 20 * 2);
        $this->getServer()->getCommandMap()->register("LOBBY_MODULE", new QuickJoinCommand());
        $this->getServer()->getPluginManager()->registerEvents(new ProxyPlayerJoinListener(), $this);
    }


    /**
     * @return GameServerProvider
     */
    public static function getGameServerProvider(): GameServerProvider
    {
        return self::$gameServerProvider;
    }

}