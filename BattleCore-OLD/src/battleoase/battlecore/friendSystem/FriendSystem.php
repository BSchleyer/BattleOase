<?php


namespace battleoase\battlecore\friendSystem;


use battleoase\battlecore\friendSystem\commands\FriendsCommand;
use battleoase\battlecore\friendSystem\database\Database;
use battleoase\battlecore\friendSystem\listener\PlayerJoinListener;
use battleoase\battlecore\friendSystem\listener\PlayerQuitListener;
use battleoase\battlecore\friendSystem\listener\ProxyPlayerJoinListener;
use battleoase\battlecore\utils\BPlugin;
use pocketmine\Server;
use pocketmine\utils\Config;

class FriendSystem extends BPlugin
{

    protected static FriendSystem $self;
    const PREFIX = "§3Battle§bOase §r§f§7";
	public static array $messages = [];
	public array $cache = [];

    public function __construct()
    {
        self::$self = $this;
        (new Database())->initializeUserTable();


        Server::getInstance()->getCommandMap()->register("friend", new FriendsCommand());
        Server::getInstance()->getPluginManager()->registerEvents(new PlayerJoinListener(), $this->getPlugin());
       // Server::getInstance()->getPluginManager()->registerEvents(new PlayerQuitListener(), $this->getPlugin());
        Server::getInstance()->getPluginManager()->registerEvents(new ProxyPlayerJoinListener(), $this->getPlugin());
    }



    /**
     * @return self
     */
    public static function getInstance(): self{
        return self::$self;
    }

    public function getMysqlPassword(): string{
        $config = new Config("/home/cloud/mysql.yml", Config::YAML);
        return $config->get("password");
    }


}