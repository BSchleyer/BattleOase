<?php


namespace battleoase\bedwars\api;


use battleoase\bedwars\BedWars;
use battleoase\bedwars\caches\TeamCache;
use battleoase\bedwars\classes\Team;
use battleoase\bedwars\player\PlayerManager;
use pocketmine\item\Bed;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class TeamAPI {


    public static function setRandomTeam(Player $player)
    {
        $config = new Config(BedWars::getInstance()->getDataFolder() . "config.yml", Config::YAML);
        $maxplayer = $config->get("maxPlayerPerTeam") * count(TeamCache::getAll());
        if($maxplayer < count(Server::getInstance()->getOnlinePlayers())) {
            $player->setGamemode(GameMode::SPECTATOR());
        } else {
            $randomteam = array_rand(TeamCache::getAll());
            $team = TeamCache::get($randomteam);
            if($team instanceof Team) {
                if(count($team->getPlayers()) >= $team->getMaxPlayer()) {
                    self::setRandomTeam($player);
                } else {
                    BedWars::getInstance()->getPlayerManager()->getPlayer($player)->setTeam($team);
                }
            }
        }

    }


    public static function getAllUsedTeams() {
        $team = 0;

        foreach (TeamCache::getAll() as $teams) {
            if(!count($teams->getPlayers()) == 0) {
                $team++;
            }

        }
        return $team;

    }

    public static function lastTeam() {
        $team = [];
        foreach (TeamCache::getAll() as $teams) {

            if(!count($teams->getPlayers()) == 0) {
                $team[] = $teams;
            }

        }
        return $team;

    }



   /* public static function teleportToSpawn(Player $player) {
        $map = BedWars::getInstance()->getArena()->getMap();
        $team = BedWars::getInstance()->getTeam()->getTeamByPlayer($player);
        $config = new Config("/root/BattleCloud/templates/" . BattleBridge::getTemplate() . "/plugin_data/BedWars/arena/{$map}.yml", Config::YAML);
        $level = $config->getNested("Spawn.{$team}.level");
        $x = $config->getNested("Spawn.{$team}.x");
        $y = $config->getNested("Spawn.{$team}.y");
        $z = $config->getNested("Spawn.{$team}.z");
        Server::getInstance()->loadLevel($map);
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $player->teleport(new Position($x, $y, $z, Server::getInstance()->getLevelByName($map)));
        $player->setHealth(20);
        $player->setXpLevel(0);
        $player->setFood(20);
        $player->getCursorInventory()->setItem(0, Item::get(0));

    }
*/

    public static function getTeamColor($team){
        if($team == "BLUE")return TextFormat::BLUE;
        if($team == "RED")return TextFormat::RED;
        if($team == "GREEN")return TextFormat::GREEN;
        if($team == "YELLOW")return TextFormat::YELLOW;
        if($team == "PINK")return "§d";
        if($team == "ORANGE")return "§6";
        if($team == "PURPLE") return "§5";
        if($team == "GRAY")return TextFormat::GRAY;
        if($team == "WHITE")return TextFormat::WHITE;
        return TextFormat::WHITE;
    }

	public static function getTeamIcon($team){
		if($team == "BLUE")return "\u{E167}";
		if($team == "RED")return "\u{E160}";
		if($team == "GREEN")return "\u{E164}";
		if($team == "YELLOW")return "\u{E165}";
		if($team == "PINK")return "\u{E162}";
		if($team == "ORANGE")return "\u{E163}";
		if($team == "PURPLE") return "\u{E161}";

		if($team == "GRAY")return "\u{E166}";
		if($team == "WHITE")return "\u{E197}";
		return "\u{E198} (NORMAL)";
	}

    public static function ColorIntToTeam(int $int): string
    {
        if ($int == 14) {return self::getTeams()[0];}
        if ($int == 11) {return self::getTeams()[1];}
        if ($int == 5) {return self::getTeams()[2];}
        if ($int == 4) {return self::getTeams()[3];}
        if ($int == 6) {return self::getTeams()[4];}
        if ($int == 1) {return self::getTeams()[5];}
        if ($int == 10) {return self::getTeams()[6];}
        if ($int == 0) {return self::getTeams()[7];}
        return  "???";
    }


    public static function getTeams(): ?array
    {
        return array_keys(TeamCache::getAll());
    }

}