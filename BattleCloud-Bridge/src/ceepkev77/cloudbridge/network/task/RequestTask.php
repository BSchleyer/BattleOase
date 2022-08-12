<?php

namespace ceepkev77\cloudbridge\network\task;

use ceepkev77\cloudbridge\CloudBridge;
use ceepkev77\cloudbridge\network\handler\PacketHandler;
use pocketmine\scheduler\Task;

class RequestTask extends Task
{

    public function onRun(): void
    {
        foreach (CloudBridge::getRequestHandler()->queue as $request) {
            PacketHandler::handleCloudPacket($request);
            CloudBridge::getRequestHandler()->unsetRequest($request);
        }
    }

}