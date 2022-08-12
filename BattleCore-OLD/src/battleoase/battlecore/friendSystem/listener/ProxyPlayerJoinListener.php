<?php


namespace battleoase\battlecore\friendSystem\listener;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\friendSystem\database\Database;
use battleoase\battlecore\friendSystem\FriendSystem;
use ceepkev77\cloudbridge\listener\cloud\ProxyPlayerJoinEvent;
use pocketmine\event\Listener;
use pocketmine\scheduler\Task;

class ProxyPlayerJoinListener implements Listener
{


	public function onJoin(ProxyPlayerJoinEvent $event){
    	$name = $event->getPlayerName();

        $countFriends = count((new Database())->getPlayerFriends($name));
        $player = $event->getPlayer();
        $requests = count((new Database())->getFriendRequests($name));
      //  if ($requests > 0) {
            $player->sendMessage(FriendSystem::PREFIX . "§cYou have currently §e{$requests} §cFriend requests§8.");
        //}
        foreach (FriendSystem::$messages as $playerName => $message) {
            if($playerName == $name) {
                $player->sendMessage($message);
            }
        }
        /* if(in_array($name, FriendSystem::$messages)) {
             $player->sendMessage(FriendSystem::$messages[$name]);
             unset(FriendSystem::$messages[$name]);
         }*/


        $buttons = [];
        foreach ((new Database())->getPlayerFriends($player->getName()) as $friend) {
            if (!is_null($friend)) {
                $buttons[] = $friend;
                sort($buttons);
            }
        }

        if ($countFriends > 0) {
         /*   $currentServer = CloudAPI::getGameServer()->getName();
            foreach ((new Database())->getPlayerFriends($name) as $friend) {
                $pk = new PlayerMessagePacket();
                $pk->message = FriendSystem::PREFIX . "§e{$name} §ais now online on §e{$currentServer}§8.";
                $pk->sendToAll = "false";
                $pk->playerName = $friend;
                $pk->sendPacket();
            }*/
        }

    }

}