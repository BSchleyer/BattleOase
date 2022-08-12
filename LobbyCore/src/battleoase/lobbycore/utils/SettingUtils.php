<?php


namespace battleoase\lobbycore\utils;


use battleoase\battlecore\BattleCore;

class SettingUtils
{
	public static function get(string $player_name): ?array {
		$query = BattleCore::getInstance()->getMysqlConnection()->query("SELECT * FROM Core.lobby_settings WHERE player_name='$player_name'");
		return $query->fetch_assoc();
	}

	public static function register(string $player_name): void {
		BattleCore::getInstance()->getMysqlConnection()->query("INSERT INTO Core.lobby_settings(player_name, hotbarSounds, doubleJump) VALUES ('$player_name', false, false)");
	}

	public static function update(string $player_name, array $update): void {
		$query = [];
		foreach($update as $key => $value) {
			$query[] = $key."='".$value."'";
		}
		$query = implode(", ", $query);
		BattleCore::getInstance()->getMysqlConnection()->query("UPDATE Core.lobby_settings SET ".$query." WHERE player_name='$player_name'");
	}
}