<?php

namespace ceepkev77\cloudbridge\network\packet;

use ceepkev77\cloudbridge\network\DataPacket;

class StartServerPacket extends DataPacket
{

    public function getPacketName(): string
    {
        return "StartServerPacket";
    }

}