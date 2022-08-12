<?php

namespace CloudBridge\network\protocol\packet;

class InvalidPacket extends Packet {

    public function getId(): int {
        return self::ID_UNKNOWN;
    }
}