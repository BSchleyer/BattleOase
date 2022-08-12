<?php


namespace battleoase\bedwars\caches;


use battleoase\bedwars\classes\Team;

class TeamCache
{
    /**
     * @var Team[]
     */
    private static $teams = [];

    public static function add(Team $team): void
    {
        if (!self::exists($team->getName())) {
            self::$teams[$team->getName()] = $team;
        }
    }

    public static function exists(string $teamName): bool
    {
        return array_key_exists($teamName, self::$teams);
    }

    public static function get(string $teamName): ?Team
    {
        if (self::exists($teamName)) {
            return self::$teams[$teamName];
        }
        return null;
    }

    public static function getAll(): array
    {
        return self::$teams;

    }

}