<?php


namespace battleoase\battlecore\externalPluginLoader;


use battleoase\battlecore\externalPluginLoader\classes\GitRepository;
use battleoase\battlecore\externalPluginLoader\task\AsyncGitPublisherTask;
use battleoase\battlecore\utils\BPlugin;
use pocketmine\utils\Config;

class ExternalPluginLoader extends BPlugin
{
    public static $AUTH_KEY = "";

    public function onEnable()
    {
        if (!file_exists($this->getPlugin()->getDataFolder() . "config.yml")) $this->getPlugin()->saveResource("config.yml");
        $config = new Config($this->getPlugin()->getDataFolder() . "config.yml", Config::YAML);
        self::$AUTH_KEY = $config->get("username") . ":" . $config->get("api_key");
        $repos = [];
        foreach ($config->get("repositories") as $repo) {
            $repos[] = new GitRepository($repo);
        }
        $this->getServer()->getAsyncPool()->submitTask(new AsyncGitPublisherTask($repos, self::$AUTH_KEY, $this->getServer()->getDataPath() . "plugins/"));

    }
}