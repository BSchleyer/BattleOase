<?php


namespace battleoase\lobbycore\player;


use pocketmine\player\Player;

class PlayerManager
{
    /** @var array  */
    public static $players = [];

    public function getPlayers(): array
    {
        return self::$players;
    }

    /**
     * @param $player
     * @return LobbyPlayer|null
     */
    public static function getPlayer($player): ?LobbyPlayer
    {
        if($player instanceof Player) {
            $playerName = $player->getName();
        } else {
            $playerName = $player;
        }

        return self::$players[$playerName] ?? null;
    }


    /**
     * @param LobbyPlayer $player
     */
    public function registerPlayer(LobbyPlayer $player)
    {
        self::$players[$player->getPlayer()->getName()] = $player;
    }


    /**
     * @param $player
     */
    public function unregisterPlayer($player)
    {
        if($player instanceof Player)
            $player = $player->getName();

        unset(self::$players[$player]);
    }

}