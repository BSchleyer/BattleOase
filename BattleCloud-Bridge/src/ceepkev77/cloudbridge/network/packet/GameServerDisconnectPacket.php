<?php

namespace ceepkev77\cloudbridge\network\packet;

use ceepkev77\cloudbridge\CloudBridge;
use ceepkev77\cloudbridge\network\DataPacket;
use pocketmine\utils\Process;

class GameServerDisconnectPacket extends DataPacket
{

    public function getPacketName(): string
    {
        return "GameServerDisconnectPacket";
    }

    public function handle()
    {
        CloudBridge::getInstance()->getLogger()->notice($this->getPacketName());
        CloudBridge::getInstance()->getServer()->shutdown();
    }

}