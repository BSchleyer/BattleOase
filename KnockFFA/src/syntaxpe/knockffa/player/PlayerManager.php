<?php


namespace syntaxpe\knockffa\player;


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
     * @param KnockPlayer $player
     */
    public function registerPlayer(KnockPlayer $player): void
    {
        self::$players[$player->getPlayer()->getName()] = $player;
    }

    /**
     * @param $player
     * @return KnockPlayer|null
     */
    public function getPlayer($player): ?KnockPlayer
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
