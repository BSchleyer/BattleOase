<?php


namespace battleoase\bedwars\caches;


use battleoase\bedwars\classes\Map;
use battleoase\bedwars\classes\Team;

class MapCache
{
	/** @var array */
	public static array $map = [];
    public static array $votes = [];
	public static array $goldVotes = [];

    public static function add(Map $map): void
    {
        if (!self::exists($map->getName())) {
            self::$map[$map->getName()] = $map;
            self::$votes[$map->getName()] = 0;
            self::$goldVotes[$map->getName()] = 0;
        }
    }

    public static function exists(string $mapName): bool
    {
        return array_key_exists($mapName, self::$map);
    }

    public static function get(string $mapName): ?Map
    {
        if (self::exists($mapName)) {
            return self::$map[$mapName];
        }
        return null;
    }

    public static function randomMap(): ?Map
	{
		return self::$map[array_rand(self::$map)];
	}

    public static function getAll(): array
    {
        return self::$map;
    }

}