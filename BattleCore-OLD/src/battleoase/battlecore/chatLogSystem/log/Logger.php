<?php


namespace battleoase\battlecore\chatLogSystem\log;


use battleoase\battlecore\BattlePlayer;
use JetBrains\PhpStorm\Pure;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\player\Player;
use pocketmine\Server;

class Logger implements Listener {

	#[Pure] public function onPlayerChat(PlayerChatEvent $event) {
		$player = $event->getPlayer();


	}
}