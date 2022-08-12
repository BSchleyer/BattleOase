<?php

namespace CloudBridge\event;

use CloudBridge\network\protocol\packet\InvalidPacket;
use CloudBridge\network\protocol\packet\Packet;
use pocketmine\event\Event;

class PacketReceiveEvent extends Event {

    private Packet $packet;
    private bool $invalid = false;

    public function __construct(Packet $packet) {
        $this->packet = $packet;

        if ($packet instanceof InvalidPacket) $this->invalid = true;
    }

    public function getPacket(): Packet {
        return $this->packet;
    }

    public function isInvalid(): bool {
        return $this->invalid;
    }
}