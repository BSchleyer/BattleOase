<?php


namespace battleoase\battlecore\privateServerSystem\api;


use ceepkev77\communicationsystem\packets\CloudPacket;
use ceepkev77\communicationsystem\packets\StartPrivateServerRequestPacket;
use ceepkev77\communicationsystem\packets\StartPrivateServerResponsePacket;
use ceepkev77\communicationsystem\packets\StopServerPacket;
use pocketmine\player\Player;

class PrivateServerAPI
{

    public static function startSevrer(string $template, Player $player) {
        /*$pk = new StartPrivateServerRequestPacket();
        $pk->owner = $player->getName();
        $pk->template = $template;
        $pk->submitRequest($pk, function (CloudPacket $packet) use ($player) {
            if($packet instanceof StartPrivateServerResponsePacket) {
                if($player->isOnline()) {
					$player->sendMessage($packet->getPrivateServerName());
					$player->transfer($packet->getPrivateServerName());
                } else {
                    $pk = new StopServerPacket();
                    $pk->addValue("serverName", $packet->getPrivateServerName());
                    $pk->sendPacket();
                }
            }
        });*/
    }

}