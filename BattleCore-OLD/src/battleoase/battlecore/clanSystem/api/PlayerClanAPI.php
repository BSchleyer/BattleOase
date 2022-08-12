<?php


namespace battleoase\battlecore\clanSystem\api;



use battleoase\battlecore\BattleCore;
use battleoase\battlecore\clanSystem\ClanSystem;
use pocketmine\player\Player;

class PlayerClanAPI
{
    public static function setPlayersClan(Player $player, $clan, $rank = ClanSystem::MEMBER)
    {
        $name = $player->getName();

		return BattleCore::getInstance()->getMysqlConnection()->query("INSERT INTO Core.Players(player_name, clan, rank) VALUES ('$name', '$clan', '$rank')");
    }

    public static function unsetPlayersClan($player)
    {
        if ($player instanceof Player) {
            $name = $player->getName();
        } else {
            $name = $player;
        }
		return BattleCore::getInstance()->getMysqlConnection()->query("DELETE FROM Core.Players WHERE player_name='$name'");
    }

    /**
     * @param $player
     * @return PlayerClan|null
     */

    public static function getPlayersClanData($player) : ?PlayerClan
    {
        if ($player instanceof Player) {
            $name = $player->getName();
        } else {
            $name = $player;
        }

        $relsult = BattleCore::getInstance()->getMysqlConnection()->query("SELECT * FROM Core.Players WHERE player_name='$name'");
        $data = null;
        if ($relsult->num_rows > 0) {
            while ($row = $relsult->fetch_assoc()) {
                $data = new PlayerClan($row['id'], $row['clan'], $row['rank']);
            }
        }
        return $data;
    }

    /**
     * @param $player
     * @return bool
     */

    public static function isInClan($player)
    {
        if ($player instanceof Player) {
            $name = $player->getName();
        } else {
            $name = $player;
        }
        $data = self::getPlayersClanData($name);
		return ($data == null ? false : true);
    }

    public static function getPlayersInClan(string $clan):array
    {
        $players = [];

        $relsult = BattleCore::getInstance()->getMysqlConnection()->query("SELECT player_name FROM Core.Players WHERE clan='$clan'");
        if ($relsult->num_rows > 0) {
            while ($row = $relsult->fetch_assoc()) {
                $players[] = $row['player_name'];
            }
        }
        return $players;
    }

}