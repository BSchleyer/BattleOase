<?php


namespace battleoase\battlecore\utils;


use battleoase\battlecore\BattleCore;
use pocketmine\plugin\PluginLogger;
use pocketmine\Server;

class BPlugin {

    /**
     * Function getPlugin
     * @return BattleCore
     */
    protected function getPlugin(): BattleCore
    {
        return BattleCore::getInstance();
    }

    /**
     * Function getServer
     * @return Server
     */
    protected function getServer(): Server
    {
        return Server::getInstance();
    }

    /**
     * Function getLogger
     * @return \AttachableLogger
     */
    protected function getLogger(): \AttachableLogger
    {
        return BattleCore::getInstance()->getLogger();
    }

}