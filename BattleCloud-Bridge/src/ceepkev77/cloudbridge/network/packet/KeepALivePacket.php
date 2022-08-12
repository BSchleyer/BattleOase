<?php

namespace ceepkev77\cloudbridge\network\packet;

use ceepkev77\cloudbridge\CloudBridge;
use ceepkev77\cloudbridge\network\DataPacket;

class KeepALivePacket extends DataPacket
{
    public function getPacketName(): string
    {
        return "KeepALivePacket";
    }

    public function handle()
    {
        $pk = $this;
        $pk->addValue("serverName", CloudBridge::getInstance()->getServer()->getMotd());
        $this->sendPacket();
    }
}