<?php


namespace battleoase\bedwars\utils;


use battleoase\bedwars\caches\MapCache;
use battleoase\bedwars\caches\TeamCache;
use battleoase\bedwars\classes\Map;
use battleoase\bedwars\classes\Team;
use pocketmine\Server;
use pocketmine\utils\Config;

class ConfigLoader
{

    public function load(string $path) {
        $config = new Config($path . "config.yml", Config::YAML);
        if($config->exists("cw")) {
            if($config->get("cw", false)) {
                Server::getInstance()->getLogger()->warning("Please Use BedWars");
            } else {
                $teams = $config->get("teams", []);
                foreach ($teams as $item) {
                    $name = $item;
                    $team = new Team();
                    $team->setName($name);
                    $team->setMaxPlayer((int) $config->get("maxPlayerPerTeam"));
                    $team->setPlayers([]);
                    TeamCache::add($team);
                }
                $maps = $config->get("registeredMaps", []);
                foreach ($maps as $item2) {
                    $mapName = $item2;
                    $map = new Map();
                    $map->setName($mapName);
                    $map->setVotes(0);
                    MapCache::add($map);
                }
            }
        } else {
            $config->setAll([
                "lobby" => "00:00:00:world",
                "teams" => [
                    "RED",
                    "BLUE",
                    "GREEN",
                    "YELLOW",
                    "PINK",
                    "ORANGE",
                    "PURPLE",
                    "WHITE",
                ],
                "registeredMaps" => [],
                "maxPlayerPerTeam" => 1,
                "minPlayerInRound" => 4,
                "countdown" => 10000,
                "cw" => false,
            ]);
            $config->save();
            Server::getInstance()->getLogger()->warning("Please Configure the Plugin");
        }

    }

}