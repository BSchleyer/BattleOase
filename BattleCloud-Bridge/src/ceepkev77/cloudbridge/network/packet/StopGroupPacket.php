<?php

namespace ceepkev77\cloudbridge\network\packet;


use ceepkev77\cloudbridge\network\DataPacket;

class StopGroupPacket extends DataPacket
{

    public function getPacketName(): string
    {
        return "StopGroupPacket";
    }

}