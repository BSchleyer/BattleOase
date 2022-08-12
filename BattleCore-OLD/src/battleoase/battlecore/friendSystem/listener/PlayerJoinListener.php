<?php

namespace battleoase\battlecore\friendSystem\listener;

use battleoase\battlecore\friendSystem\database\Database;
use battleoase\battlecore\friendSystem\FriendSystem;
use ceepkev77\cloudapi\CloudAPI;
use ceepkev77\communicationsystem\packets\CloudPacket;
use ceepkev77\communicationsystem\packets\CloudPlayerRequestPacket;
use ceepkev77\communicationsystem\packets\CloudPlayerResponsePacket;
use ceepkev77\communicationsystem\packets\PlayerMessagePacket;
use Exception;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\Server;

class PlayerJoinListener implements Listener
{
	private static string $friends;
	private static array $onlineArray;

	/**
	 * @throws Exception
	 */
	public function onLogin(PlayerLoginEvent $event): void
	{
		$player = $event->getPlayer();
		(new Database())->addPlayer($player);
	}


	public function onJoin(PlayerJoinEvent $event)
	{
		$player = $event->getPlayer();
		$name = $player->getName();

		$requests = count((new Database())->getFriendRequests($name));

		$buttons = [];
		foreach ((new Database())->getPlayerFriends($player->getName()) as $friend) {
			if (!is_null($friend)) {
				$buttons[] = $friend;
				sort($buttons);
			}
		}
	}
}