<?php

namespace ceepkev77\cloudbridge\network\packet;


use ceepkev77\cloudbridge\CloudBridge;
use ceepkev77\cloudbridge\listener\cloud\ProxyPlayerQuitEvent;
use ceepkev77\cloudbridge\network\DataPacket;

class ProxyPlayerQuitPacket extends DataPacket
{

    public function getPacketName(): string
    {
        return "ProxyPlayerQuitPacket";
    }

    public function handle()
    {
        $ev = new ProxyPlayerQuitEvent($this->data["playerName"]);
        $ev->call();
        if(in_array($this->data["playerName"], CloudBridge::$qeueuPlayer)) {
            unset(CloudBridge::$qeueuPlayer[$this->data["playerName"]]);
        }
        parent::handle(); // TODO: Change the autogenerated stub
    }

}