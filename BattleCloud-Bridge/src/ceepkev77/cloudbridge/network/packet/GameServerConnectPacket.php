<?php

namespace ceepkev77\cloudbridge\network\packet;

use ceepkev77\cloudbridge\network\DataPacket;

class GameServerConnectPacket extends DataPacket
{

    public function getPacketName(): string
    {
        return "GameServerConnectPacket";
    }


}