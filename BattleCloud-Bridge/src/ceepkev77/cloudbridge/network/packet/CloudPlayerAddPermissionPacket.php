<?php

namespace ceepkev77\cloudbridge\network\packet;

use ceepkev77\cloudbridge\network\DataPacket;

class CloudPlayerAddPermissionPacket extends DataPacket
{

    public string $playerName;
    public string $permission;

    public function getPacketName(): string
    {
        return "CloudPlayerAddPermissionPacket";
    }

    public function encode()
    {
        $this->addValue("playerName", $this->playerName);
        $this->addValue("permission", $this->permission);
        return parent::encode();
    }


}