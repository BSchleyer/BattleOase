<?php

namespace SignSystem\provider;

use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\MainLogger;
use pocketmine\world\Position;
use pocketmine\world\WorldManager;
use SignSystem\objects\GroupSign;
use SignSystem\SignSystem;
use SignSystem\task\SignTask;
use SignSystem\utils\Math;

class SignProvider {

    private Config $config;
    /** @var GroupSign[] */
    private array $signs = [];

    public function __construct() {
        $this->config = new Config(SignSystem::getInstance()->getDataFolder() . "signs.json", Config::JSON);
        $this->signs = [];
        $this->loadSigns();
    }

    public function loadSigns() {
        foreach ($this->getSignsConfig() as $sign) {
            $world = Server::getInstance()->getWorldManager()->getWorldByName($sign["levelName"]);
            $vector = Math::stringVectorToVector3($sign["position"]);
            if ($world === null) continue;
            $this->signs[] = new GroupSign($sign["groupName"], new Position($vector->getX(), $vector->getY(), $vector->getZ(), $world), boolval($sign["maintenance"]));
        }
        SignSystem::getInstance()->getScheduler()->scheduleRepeatingTask(new SignTask(), SignSystem::getInstance()->getSignConfig()->getReload() * 20);
    }

    /** @throws \JsonException */
    public function addSign(String $groupName, Position $position): void {
        $sign = $this->getSignsConfig();
        $sign[] = ["groupName" => $groupName, "position" => Math::vector3ToString($position->asVector3()), "levelName" => $position->getWorld()->getFolderName(), "maintenace" => false];
        $this->getConfig()->set("signs", $sign);
        $this->getConfig()->save();
        $this->signs[] = new GroupSign($groupName, $position, false);
    }

    /** @throws \JsonException */
    public function removeSign(Position $position): void {
        $signs = $this->getSignsConfig();
        $i = 0;
        foreach ($signs as $signData) {
            if ($signData["position"] == Math::vector3ToString($position->asVector3()) && $signData["levelName"] == $position->getWorld()->getFolderName()) break;
            $i++;
        }
        unset($signs[$i]);
        $this->getConfig()->set("signs", $signs);
        $this->getConfig()->save();
    }

    public function getSignByPosition(Position $position): ?GroupSign {
        foreach ($this->getSigns() as $sign) {
            if ($sign->getPosition()->equals($position)) return $sign;
        }
        return null;
    }

    public function getSignsConfig(): array {
        return $this->getConfig()->get("signs", []);
    }

    /*** @return GroupSign[] */
    public function getSigns(): array {
        return $this->signs;
    }

    public function getConfig(): Config {
        return $this->config;
    }
}