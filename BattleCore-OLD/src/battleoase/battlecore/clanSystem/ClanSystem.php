<?php


namespace battleoase\battlecore\clanSystem;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\clanSystem\commands\ClanCommand;
use battleoase\battlecore\utils\BPlugin;
use pocketmine\item\CookedChicken;
use pocketmine\utils\TextFormat;

class ClanSystem extends BPlugin {

	const PREFIX = TextFormat::GRAY.TextFormat::YELLOW."ClanSystem".TextFormat::GRAY. " ";

	public int $CLAN_STATE = 0;
	public int $ELO = 0;
	public int $LOSES_CW = 0;
	public int $WINS_CW = 0;

	public string $CUSTOM_INFO = "This is a Default Custom Info!";
	public string $COLOR = "WHITE";

	public function __construct() {
		$this->getServer()->getCommandMap()->register("clan", new ClanCommand());

		//BattleCore::getInstance()->getMysqlConnection()->query("INSERT INTO Core.clans(`name`, `tag`, `elo`, `state`, `owner`, `created_at`,`color`, `custom_info`,`loses_cw`,`wins_cw`) VALUES ('$clan_name', '$clan_tag', '$elo', '$state', '$owner_name', '$created_at', '$color', '$custom_info', '$loses_cw', '$wins_cw')");
		//BattleCore::getInstance()->getMysqlConnection()->query("CREATE TABLE IF NOT EXISTS Core.Clans(id INTEGER NOT NULL KEY AUTO_INCREMENT, clan_name varchar(64) NOT NULL UNIQUE, clan_tag varchar(64) NOT NULL UNIQUE, owner varchar(64) NOT NULL UNIQUE)");
		//BattleCore::getInstance()->getMysqlConnection()->query("CREATE TABLE IF NOT EXISTS Core.Players(id INTEGER NOT NULL KEY AUTO_INCREMENT, player_name varchar(64) NOT NULL UNIQUE, clan varchar(64) NOT NULL, rank VARCHAR(64) NOT NULL)");
		//BattleCore::getInstance()->getMysqlConnection()->query("CREATE TABLE IF NOT EXISTS Core.Invites(id INTEGER NOT NULL KEY AUTO_INCREMENT, invited_player varchar(64) NOT NULL, clan varchar(64) NOT NULL)");
	}

	const COLORS = [
		"WHITE" => TextFormat::WHITE,
		"BLUE" => TextFormat::BLUE,
		"RED" => TextFormat::RED,
		"GREEN" => TextFormat::GREEN,
		"YELLOW" => TextFormat::YELLOW,
		"PINK" => "§d",
		"ORANGE" => "§6",
		"PURPLE" => "§5",
		"GRAY" => TextFormat::GRAY
	];


	const LEADER = 1;
	const MODERATOR = 2;
	const MEMBER = 3;

	public static function rankIntToString(int $rank): string {
		if ($rank === 1) return "LEADER";
		if ($rank === 2) return "MODERATOR";
		if ($rank === 3) return "MEMBER";
		return "MEMBER";
	}

	public static function rankStringToColor(string $rank): string {
		if ($rank === "LEADER") return "§c";
		if ($rank === "MODERATOR") return "§3";
		if ($rank === "MEMBER") return "§7";
		return "MEMBER";
	}

	public static function stateIntToString(int $state): string {
		// 0: Only Invites  1: Open  2: Closed
		if ($state === 0) return "§6Only Invite";
		if ($state === 1) return "§aOpen";
		if ($state === 2) return "§cClosed";
		return "§7Only Invite";
	}

	public static function count_words(string $string): int {
		$string = str_replace("&#039;", "'", $string);
		$t = array(' ', "\t", '=', '+', '-', '*', '/', '\\', ',', '.', ';', ':', '[', ']', '{', '}', '(', ')', '<', '>', '&', '%', '$', '@', '#', '^', '!', '?', '~'); // separators
		$string = str_replace($t, " ", $string);
		$string = trim(preg_replace("/\s+/", " ", $string));
		$num = 0;
		if (mb_strlen($string) > 0) {
			$word_array = explode(" ", $string);
			$num = count($word_array);
		}
		return $num;
	}
}