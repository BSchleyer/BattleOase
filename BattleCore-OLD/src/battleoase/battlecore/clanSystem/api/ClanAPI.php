<?php


namespace battleoase\battlecore\clanSystem\api;

use battleoase\battlecore\BattleCore;
use DateTime;
use pocketmine\player\Player;

class ClanAPI
{
    public static function createClan(Player $player,string $clan_name,string $clan_tag, string $color = "WHITE")
    {
        $name = $player->getName();
        $clan_name = str_replace('§', '', str_replace('\n', '', $clan_name));
        $clan_tag = str_replace('§', '', str_replace('\n', '', $clan_tag));

        $elo = BattleCore::getInstance()->clanSystem->ELO;
		$state = BattleCore::getInstance()->clanSystem->CLAN_STATE;
		$custom_info = BattleCore::getInstance()->clanSystem->CUSTOM_INFO;

		$loses_cw = BattleCore::getInstance()->clanSystem->LOSES_CW;
		$wins_cw = BattleCore::getInstance()->clanSystem->WINS_CW;

		$created_at = (new DateTime("now", new \DateTimeZone("Europe/Berlin")))->format("d.m.Y-H:i:s"); // cet timezone

		//return BattleCore::getInstance()->getMysqlConnection()->query("INSERT INTO Core.Clans(clan_name, clan_tag, owner) VALUES ('$clan_name', '$clan_tag', '$name')");
		BattleCore::getInstance()->getMysqlConnection()->query("INSERT INTO Core.Clans(`clan_name`, `clan_tag`, `elo`, `state`, `owner`, `created_at`,`color`, `custom_info`,`loses_cw`,`wins_cw`) VALUES ('$clan_name', '$clan_tag', '$elo', '$state', '$name', '$created_at', '$color', '$custom_info', '$loses_cw', '$wins_cw')");
    }



    public static function removeClan($clan)
    {
		return BattleCore::getInstance()->getMysqlConnection()->query("DELETE FROM Core.Clans WHERE clan_name='$clan'");
    }

    /**
     * @param $clan
     * @return bool
     */

    public static function isClan($clan)
    {

        $relsult = BattleCore::getInstance()->getMysqlConnection()->query("SELECT clan_name FROM Core.Clans WHERE clan_name='$clan'");
        if ($relsult->num_rows > 0) {
            return true;
        }
        return false;
    }

    /**
     * @param string $clan_tag
     * @return bool
     */


    public static function isClanTag(string  $clan_tag)
    {
        $relsult = BattleCore::getInstance()->getMysqlConnection()->query("SELECT clan_name FROM Core.Clans WHERE clan_tag='$clan_tag'");
        if ($relsult->num_rows > 0) {
            return true;
        }
        return false;
    }

    /**
     * @param $clan
     * @return Clan|null
     */
    public static function getClan($clan) : ?Clan
    {

        //$clan_tag = $clan->getClanTag();
        $relsult = BattleCore::getInstance()->getMysqlConnection()->query("SELECT * FROM Core.Clans WHERE clan_name='$clan'");
        $return = null;
        if ($relsult->num_rows > 0) {
            while ($row = $relsult->fetch_assoc()) {
                $return = new Clan($row['clan_name'], $row['clan_tag'], $row["elo"], $row["state"], $row['owner'], $row["created_at"], $row["color"], $row["custom_info"], $row["loses_cw"], $row["wins_cw"]);
            }
        }
        return $return;
    }
}