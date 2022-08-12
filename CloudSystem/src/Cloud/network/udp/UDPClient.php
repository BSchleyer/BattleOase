<?php

namespace Cloud\network\udp;

use Cloud\network\CloudSocket;
use Cloud\network\protocol\packet\Packet;
use Cloud\network\utils\Address;

class UDPClient {

    private Address $address;

    public function __construct(Address $address) {
        $this->address = $address;
    }

    public function sendPacket(Packet $packet) {
        $packet->encode();
        $json = json_encode($packet->getPacketContent());
        if ($json !== false) {
            CloudSocket::getInstance()->getUdpServer()->write($json, $this->address);
        }
    }

    public function getAddress(): Address {
        return $this->address;
    }
}