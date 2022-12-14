<?php

namespace ceepkev77\cloudbridge\network\packet;

use ceepkev77\cloudbridge\network\DataPacket;

class PlayerMovePacket extends DataPacket
{

    public $playerName;
    public $toServer;

    public function getPacketName(): string
    {
        return "PlayerMovePacket";
    }

    public function encode()
    {
        $this->addValue("playerName", $this->playerName);
        $this->addValue("toServer", $this->toServer);
        return parent::encode(); // TODO: Change the autogenerated stub
    }

}