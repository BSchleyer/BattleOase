<?php

namespace ceepkev77\lobbyapi\provider;

use ceepkev77\cloudbridge\objects\GameServer;
use pocketmine\player\Player;

class GameServerProvider
{

    /** @var array $servers */
    public static $servers = [];

    public function getGameServers(): array
    {
        return self::$servers;
    }

    /**
     * @param GameServer $server
     */
    public function registerGameServer(GameServer $server): void
    {
        self::$servers[$server->getName()] = $server;
    }


    /**
     * @param $server
     * @return GameServer|null
     */
    public function getGameServer($server): ?GameServer
    {
        if($server instanceof GameServer) {
            $serverName = $server->getName();
        } else {
            $serverName = $server;
        }

        return self::$servers[$serverName] ?? null;
    }

    public function getTemplates(): array {
        $return = [];
        foreach (($this->getGameServers() ?? []) as $item) {
            if($item instanceof GameServer) {
                if(!in_array($item->getCloudGroup()->getName(), $return)) {
                    $return[] = $item->getCloudGroup()->getName();
                }
            }
        }
        return $return;
    }

    public function getServerByTemplate(String $template): array {
        $return = [];
        foreach (($this->getGameServers() ?? []) as $server) {
            if($server instanceof GameServer) {
                if($server->getCloudGroup()->getName() === $template) {
                    $return[] = $server->getName();
                }
            }
        }
        return $return;
    }


    /**
     * @param $server
     */
    public function unregisterServer($server): void
    {
        if($server instanceof GameServer)
            $server = $server->getName();
        unset(self::$servers[$server]);
    }



}