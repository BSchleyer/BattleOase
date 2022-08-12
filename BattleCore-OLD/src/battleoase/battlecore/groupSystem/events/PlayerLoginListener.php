<?php


namespace battleoase\battlecore\groupSystem\events;



use battleoase\battlecore\BattleCore;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use battleoase\battlecore\groupSystem\GroupSystem;

class PlayerLoginListener implements Listener
{

	public function onLogin(PlayerLoginEvent $event){
		$player = $event->getPlayer();
		$name = $player->getName();

		$default_group = GroupSystem::DEFAULT_GROUP;

		if (!array_key_exists($name, GroupSystem::$Skins)) {
			GroupSystem::$Skins[$name] = $player->getSkin();
		}

		BattleCore::getInstance()->getMysqlConnection()->query("INSERT INTO Core.group_players(`player_name`, `group_name`,  `nick`, `skin_player_name`, `color`) VALUES ('$name', '$default_group', 'NULL', 'NULL', '§7')");
	}

}