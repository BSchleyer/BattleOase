<?php

namespace ceepkev77\cloudbridge\network\packet;

use ceepkev77\cloudbridge\network\DataPacket;

class AddPlayerToCWQueuePacket extends DataPacket
{

    public string $playerName;
    public string $clanName;

    public function getPacketName(): string
    {
        return "AddPlayerToCWQueuePacket";
    }

    public function encode()
    {
        $this->addValue("playerName", $this->playerName);
        $this->addValue("clanName", $this->clanName);
        return parent::encode();
    }


}