<?php


namespace battleoase\bedwars\player;


use pocketmine\player\Player;

class PlayerManager
{
    /** @var array $players */
    public static array $players = [];

    public function getPlayers(): array
    {
        return self::$players;
    }

    /**
     * @param BedWarsPlayer $player
     */
    public function registerPlayer(BedWarsPlayer $player): void
    {
        self::$players[$player->getPlayer()->getName()] = $player;
    }


    /**
     * @param $player
     * @return BedWarsPlayer|null
     */
    public function getPlayer($player): ?BedWarsPlayer
    {
        if($player instanceof Player) {
            $playerName = $player->getName();
        } else {
            $playerName = $player;
        }

        return self::$players[$playerName] ?? null;
    }


    /**
     * @param $player
     */
    public function unregisterPlayer($player): void
    {
        if($player instanceof Player)
            $player = $player->getName();

        unset(self::$players[$player]);
    }

}
