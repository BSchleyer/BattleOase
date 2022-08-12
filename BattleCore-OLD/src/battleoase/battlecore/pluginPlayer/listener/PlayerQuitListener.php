<?php


namespace battleoase\battlecore\pluginPlayer\listener;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\BattlePlayer;
use battleoase\battlecore\pluginPlayer\player\BPlayer;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;

class PlayerQuitListener implements Listener
{
	public function onQuit(PlayerQuitEvent $event){
        /** @var BattlePlayer $player */
		$player = $event->getPlayer();
		$name = $player->getName();

		$date = new \DateTime("now", new \DateTimeZone("Europe/Berlin"));
		$format = $date->format("H:i:s-d.m.Y");
        BattleCore::getInstance()->getMysqlConnection()->query("UPDATE Core.players SET `last-seen`= '$format' WHERE `player_name`='$name'");

	}
}