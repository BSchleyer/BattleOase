<?php

namespace xxFLORII\Cores\API;

use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\world\Position;
use xxFLORII\Cores\Main;

class CoresAPI {

    public function randomTeam(Player $player): string{
        $randomteam = Main::$teams[mt_rand(0, (count(Main::$teams) - 1))];

        $color = ($randomteam === "red") ? "§4" : "§1";

		if ($randomteam === "red"){
			if (!in_array($player->getName(), Main::$redTeam) && count(Main::$redTeam) < Main::MAX_PLAYERS_PER_TEAM){
				Main::$redTeam[] = $player->getName();
				unset(Main::$blueTeam[$player->getName()]);
			} else {
				$this->randomTeam($player);
			}
		} elseif ($randomteam === "blue"){
			if (!in_array($player->getName(), Main::$blueTeam) && count(Main::$redTeam) < Main::MAX_PLAYERS_PER_TEAM){
				Main::$blueTeam[] = $player->getName();
				unset(Main::$redTeam[$player->getName()]);
			} else {
				$this->randomTeam($player);
			}
		}
        $player->sendMessage(Main::getPrefix() . "§aYou are now in team {$color}{$randomteam}");
        return ($color.$randomteam);
    }

    public function copymap($src, $dst)
    {

        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->copymap($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    public function deleteDirectory($dirPath)
    {
        if (is_dir($dirPath)) {
            $objects = scandir($dirPath);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dirPath . DIRECTORY_SEPARATOR . $object) == "dir") {
                        $this->deleteDirectory($dirPath . DIRECTORY_SEPARATOR . $object);
                    } else {
                        unlink($dirPath . DIRECTORY_SEPARATOR . $object);
                    }
                }
            }
            reset($objects);
            rmdir($dirPath);
        }
    }

    public function deletePlayer(Player $player){
        if (in_array($player->getName(), Main::$redTeam)){
            unset(Main::$redTeam[array_search($player->getName(), Main::$redTeam)]);
            var_dump(Main::$redTeam);
        } elseif (in_array($player->getName(), Main::$blueTeam)){
            unset(Main::$blueTeam[array_search($player->getName(), Main::$blueTeam)]);
            var_dump(Main::$blueTeam);
        }
    }

    public function giveKit(Player $player)
    {
        $helm = ItemFactory::getInstance()->get(298, 0, 1);
        $chest = ItemFactory::getInstance()->get(299, 0, 1);
        $leg = ItemFactory::getInstance()->get(300, 0, 1);
        $boots = ItemFactory::getInstance()->get(301, 0, 1);

        $player->getInventory()->clearAll();
        $player->getInventory()->addItem(ItemFactory::getInstance()->get(272, 0, 1));
        $player->getInventory()->addItem(ItemFactory::getInstance()->get(274, 0, 1));
        $player->getInventory()->addItem(ItemFactory::getInstance()->get(261, 0, 1));
        $player->getInventory()->addItem(ItemFactory::getInstance()->get(297, 0, 32));
        $player->getInventory()->addItem(ItemFactory::getInstance()->get(322, 0, 16));
        $player->getInventory()->addItem(ItemFactory::getInstance()->get(17, 0, 64));
        $player->getInventory()->addItem(ItemFactory::getInstance()->get(17, 0, 64));
        $player->getInventory()->addItem(ItemFactory::getInstance()->get(275, 0, 1));
        $player->getInventory()->addItem(ItemFactory::getInstance()->get(262, 0, 8));
        $player->getArmorInventory()->setHelmet($helm);
        $player->getArmorInventory()->setChestplate($chest);
        $player->getArmorInventory()->setLeggings($leg);
        $player->getArmorInventory()->setBoots($boots);
    }

    public function spawn(Player $player)
    {
        $pos = $player->getPosition();
        $player->setSpawn($pos);
    }

	public static function giveLobbyItems(Player $player){
		$inv = $player->getInventory();
		$team = ItemFactory::getInstance()->get(ItemIds::CHEST);
		$team->setCustomName("§eTeams");

		$inv->setItem(1, $team);
	}

    public function teleportIngame(Player $player)
    {
        $config = Main::getInstance()->getConfig();
        $level = Server::getInstance()->getWorldManager()->getWorldByName($config->get("Arena"));
        $af = new Config(Main::getInstance()->getDataFolder() . "/" . $config->get("Arena") . ".yml", Config::YAML);
        if (in_array($player->getName(), Main::$redTeam)) {
            $player->teleport(new Position($af->get("s1x"), $af->get("s1y") + 1, $af->get("s1z"), $level));
        } else if (in_array($player->getName(), Main::$blueTeam)) {
            $player->teleport(new Position($af->get("s2x"), $af->get("s2y") + 1, $af->get("s2z"), $level));
        }
    }

	public function joinTeam(Player $player, string $team){

		unset(Main::$blueTeam[array_search($player->getName(), Main::$blueTeam)]);
		unset(Main::$redTeam[array_search($player->getName(), Main::$redTeam)]);

    	$color = ($team === "red") ? "§4" : "§1";
		if ($team === "red"){
			if (!in_array($player->getName(), Main::$redTeam) && count(Main::$redTeam) < Main::MAX_PLAYERS_PER_TEAM){
				Main::$redTeam[] = $player->getName();
			} else {
				$player->sendMessage(Main::getPrefix() . "§cThis team is already full.");
			}
		} elseif ($team === "blue"){
			if (!in_array($player->getName(), Main::$blueTeam) && count(Main::$redTeam) < Main::MAX_PLAYERS_PER_TEAM){
				Main::$blueTeam[] = $player->getName();
			} else {
				$player->sendMessage(Main::getPrefix() . "§cThis team is already full.");
			}
		}
		$player->sendMessage(Main::getPrefix() . "§aYou are now in team {$color}{$team}");
	}
}