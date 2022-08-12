<?php


namespace battleoase\battlecore\clanSystem\api;


use battleoase\battlecore\BattleCore;
use iTzFreeHD\ClanSystem\ClanSystem;
use pocketmine\Player;

class InviteAPI
{

    /**
     * @param string $invited_player
     * @param string $clan
     * @return bool|\mysqli_result
     */

    public static function invitePlayer(string $invited_player,string $clan)
    {
		return BattleCore::getInstance()->getMysqlConnection()->query("INSERT INTO Core.Invites(invited_player, clan) VALUES ('$invited_player', '$clan')");
    }

    /**
     * @param string $invited_player
     * @param string $clan
     * @return bool|\mysqli_result
     */

    public static function removeInvite(string $invited_player,string $clan)
    {
		//var_dump(mysqli_error(ClanSystem::getSql()));
        return BattleCore::getInstance()->getMysqlConnection()->query("DELETE FROM Core.Invites WHERE invited_player='$invited_player' AND clan='$clan'");
    }

    /**
     * @param string $invited_player
     * @param string $clan
     * @return bool
     */

    public static function hasInvite(string $invited_player,string $clan)
    {
        $relsult = BattleCore::getInstance()->getMysqlConnection()->query("SELECT * FROM Core.Invites WHERE invited_player='$invited_player' AND clan='$clan'");
        if ($relsult->num_rows > 0) {
            return true;
        }
        return false;
    }

    /**
     * @param string $player
     * @return array
     */

    public static function getInvites(string $player)
    {
        $return = [];
        $relsult = BattleCore::getInstance()->getMysqlConnection()->query("SELECT * FROM Core.Invites WHERE invited_player='$player'");
        if ($relsult->num_rows > 0) {
            while ($row = $relsult->fetch_assoc()) {
                $return[] = $row['clan'];
            }
        }
        return $return;
    }

}