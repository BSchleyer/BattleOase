<?php

namespace battleoase\battlecore\npcSystem\utils;

use battleoase\battlecore\npcSystem\caches\TagCache;
use battleoase\battlecore\npcSystem\caches\TypeCache;
use battleoase\battlecore\npcSystem\classes\AssignableTag;
use battleoase\battlecore\npcSystem\classes\CustomType;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\MainLogger;

class ConfigLoader
{
	public function load(string $path)
	{
		$config = new Config($path . "config.yml", Config::YAML);
		$customs = $config->get("enabled-customs");
		foreach ($customs as $custom) {
			$name = $custom["name"];
			if (file_exists($path . $name . "_geometry.json")) {
				$geometry = file_get_contents($path . $name . "_geometry.json");
				if (file_exists($path . $name . "_skin.png")) {
					$skinData = $this->getFromPathBytes($path . $name . "_skin.png");
					$type = new CustomType();
					$type->setGeometry($geometry);
					$type->setImageData($skinData);
					$type->setGeometryName($custom["geometry-name"]);
					$type->setName($name);
					TypeCache::add($type);
				} else {
					Server::getInstance()->getLogger()->warning("Could not find Skin File For Custom Entity \"" . $custom . "\"");
					continue;
				}
			} else {
				Server::getInstance()->getLogger()->warning("Could not find Geometry File For Custom Entity \"" . $custom . "\"");
				continue;
			}
		}
	}

	public function getFromPathBytes(string $path): string
	{
		$img = imagecreatefrompng($path);
		$bytes = '';
		$l = getimagesize($path);
		for ($y = 0; $y < $l[1]; $y++) {
			for ($x = 0; $x < $l[0]; $x++) {
				$rgba = imagecolorat($img, $x, $y);
				$a = ((~((int)($rgba >> 24))) << 1) & 0xff;
				$r = ($rgba >> 16) & 0xff;
				$g = ($rgba >> 8) & 0xff;
				$b = $rgba & 0xff;
				$bytes .= chr($r) . chr($g) . chr($b) . chr($a);
			}
		}
		return $bytes;
	}
}