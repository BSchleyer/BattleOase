<?php

namespace battleoase\battlecore\friendSystem\listener;

use battleoase\battlecore\BattleCore;
use battleoase\battlecore\friendSystem\database\Database;
use battleoase\battlecore\friendSystem\FriendSystem;
use ceepkev77\communicationsystem\packets\CloudPacket;
use ceepkev77\communicationsystem\packets\CloudPlayerRequestPacket;
use ceepkev77\communicationsystem\packets\CloudPlayerResponsePacket;
use ceepkev77\communicationsystem\packets\PlayerMessagePacket;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\scheduler\Task;

class PlayerQuitListener implements Listener {

    public function onQuit(PlayerQuitEvent $event){
        $player = $event->getPlayer();
        $name = $player->getName();
        /*BattleCore::getInstance()->getScheduler()->scheduleDelayedTask(new class($name) extends Task{
            protected $name;
            public function __construct(string $name){
                $this->name = $name;
            }

            public function onRun(): void
            {
                foreach ((new Database())->getPlayerFriends($this->name) as $friend){
                    $pk1 = new CloudPlayerRequestPacket();
                    $pk1->playerName = $this->name;
                    $name = $this->name;
                    $pk1->submitRequest($pk1, function (CloudPacket $packet) use ($name, $friend){
                        if($packet instanceof CloudPlayerResponsePacket) {
                            if($packet->getPlayerServer() === null) {
                                $pk = new PlayerMessagePacket();
                                $pk->message = FriendSystem::PREFIX . "§e{$name} §chas left the network§8.";
                                $pk->sendToAll = "false";
                                $pk->playerName = $friend;
                                $pk->sendPacket();
                            }
                        }
                    });
                }
            }
        }, 100);*/
    }
}