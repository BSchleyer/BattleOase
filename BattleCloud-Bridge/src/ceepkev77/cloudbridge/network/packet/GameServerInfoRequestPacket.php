<?php

namespace ceepkev77\cloudbridge\network\packet;

use ceepkev77\cloudbridge\network\RequestPacket;

class GameServerInfoRequestPacket extends RequestPacket {

    public ?string $server = null;

    public function getPacketName(): string
    {
        return "GameServerInfoRequestPacket";
    }

    public function encode()
    {
        if($this->server !== null) {
            $this->addValue("serverInfoName", $this->server);
        }
        return parent::encode();
    }

}