<?php

namespace SignSystem;

use ceepkev77\lobbyapi\LobbyAPI;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use SignSystem\commands\SignCommand;
use SignSystem\config\SignConfig;
use SignSystem\listener\EventListener;
use SignSystem\provider\SignProvider;

class SignSystem extends PluginBase {

    const PREFIX = "§aSign§2System §r§8× §7";

    private static self $instance;
    public array $ignoredPlayers = [];
    private SignConfig $signConfig;
    private SignProvider $signProvider;

    protected function onEnable(): void {
        self::$instance = $this;
        $this->signConfig = new SignConfig("/home/cloud/data/signsystem/config.yml", Config::YAML);
        $this->signProvider = new SignProvider();
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        $this->getServer()->getCommandMap()->register("signSystem", new SignCommand("sign", "Main SignSystem Command", "", ["signsystem"]));

        foreach (scandir($this->getServer()->getDataPath() . "worlds/") as $file) {
            if ($file == "." || $file == "..") continue;
            $this->getServer()->getWorldManager()->loadWorld($file);
        }
    }


    public function getSignProvider(): SignProvider {
        return $this->signProvider;
    }

    public function getSignConfig(): SignConfig {
        return $this->signConfig;
    }

    public static function getInstance(): SignSystem {
        return self::$instance;
    }

    public function getAllServer(): array {
        $services = [];
        foreach (LobbyAPI::getGameServerProvider()->getTemplates() as $template) {
            $services[$template] = LobbyAPI::getGameServerProvider()->getServerByTemplate($template);
        }
        return $services;
    }

    public function getAllGroups(): array {
        return LobbyAPI::getGameServerProvider()->getTemplates();
    }

    public static function getPrefix(): string {
        return self::PREFIX;
    }
}