<?php

namespace ceepkev77\cloudbridge\listener\cloud;

use ceepkev77\cloudbridge\network\DataPacket;
use pocketmine\event\Cancellable;
use pocketmine\event\player\PlayerEvent;

class CloudPacketReceiveEvent extends PlayerEvent implements Cancellable
{

    private DataPacket|string $packet;

    public function __construct(DataPacket|string $packet)
    {
        $this->packet = $packet;
    }


    /**
     * @return DataPacket|string
     */
    public function getPacket(): DataPacket|string
    {
        return $this->packet;
    }

    /**
     * @return DataPacket|string
     */
    public function getPacketClass() : DataPacket|string
    {
        return $this->packet;
    }

    /**
     * @return string
     */
    public function getPacketName() : string
    {
        return $this->packet->data["packetName"];
    }


    public function isCancelled(): bool
    {
        return false;
    }
}