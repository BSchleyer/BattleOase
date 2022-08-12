<?php


namespace battleoase\lobbycore\utils;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\BattlePlayer;
use pocketmine\player\Player;
use pocketmine\Server;

class FeatureUtils
{

	public static function setBuyItem($feature, Player $player, $cost)
	{
		if ($player instanceof BattlePlayer){
				$name = $player->getName();

				$date = new \DateTime("now", new \DateTimeZone("Europe/Berlin"));
				$format = $date->format("H:i:s-d.m.Y");
				$transaction_id = BattleCore::getInstance()->generateRandomString("10");

				BattleCore::getInstance()->getMysqlConnection()->query("INSERT INTO Core.`lobby_feature_buys`(`player_name`, `feature`, `date_time`, `transaction_id`) VALUES ('{$name}','{$feature}','$format', 'LB-F-$transaction_id')");
				$player->removeCoins($cost);
				return true;
		}
	}

	/**
	 * @param $feature
	 * @param $playername
	 * @return bool
	 */
	public static function hasBuyItem($feature, $playername) : bool
	{
		return BattleCore::getInstance()->getMysqlConnection()->query("SELECT * FROM Core.lobby_feature_buys WHERE `player_name`='$playername' AND `feature`='$feature'")->num_rows === 1;
	}

	/**
	 * @param $feature
	 * @param $playername
	 */
	public static function setItemFeature($feature, $playername)
	{
		//Todo:: Tobias seine Mysql USEN
		//BattleCore::getInstance()->getMysqlConnection()->query("INSERT INTO lobby_feature(`player_name`, `item`, `pet`, `block`) VALUES ('$playername', NULL , NULL, NULL )");
		BattleCore::getInstance()->getMysqlConnection()->query("UPDATE Core.lobby_feature SET `item`='$feature' WHERE `player_name`= '$playername'");
	}

	/**
	 * @param $feature
	 * @param $playername
	 */
	public static function setBlockFeature($feature, $playername)
	{
		BattleCore::getInstance()->getMysqlConnection()->query("UPDATE Core.lobby_feature SET `block`='$feature' WHERE `player_name`= '$playername'");

	}

	/**
	 * @param $feature
	 * @param $playername
	 */
	public static function setPetFeature($feature, $playername)
	{
		BattleCore::getInstance()->getMysqlConnection()->query("UPDATE Core.lobby_feature SET `pet`='$feature' WHERE `player_name`= '$playername'");
	}

	/**
	 * @param $playername
	 * @return array
	 */
	public static function getFeature($playername) : array
	{
		$result = BattleCore::getInstance()->getMysqlConnection()->query("SELECT * FROM Core.lobby_feature WHERE player_name='$playername'");
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				return ["pet" => $row["pet"], "item" => $row["item"], "block" => $row["block"]];
			}
		}
	}

}