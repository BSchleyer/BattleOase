<?php


namespace battleoase\battlecore\groupSystem;


use battleoase\battlecore\groupSystem\api\PlayerAPI;
use battleoase\battlecore\groupSystem\commands\GroupCommand;
use battleoase\battlecore\groupSystem\commands\NickCommand;
use battleoase\battlecore\groupSystem\commands\NicklistCommand;
use battleoase\battlecore\groupSystem\events\PlayerChatListener;
use battleoase\battlecore\groupSystem\events\PlayerJoinListener;
use battleoase\battlecore\groupSystem\events\PlayerLoginListener;
use battleoase\battlecore\groupSystem\events\PlayerQuitListener;
use battleoase\battlecore\groupSystem\objects\Group;
use battleoase\battlecore\utils\BPlugin;
use pocketmine\Server;
use pocketmine\utils\Config;

class GroupSystem extends BPlugin
{

    /** @var GroupSystem */
    private static $instance;

    /** @var PlayerAPI  */
    private static PlayerAPI $playerApi;

    /** @var array $skins */
    public static array $skins = [];

    /** @var array<Group> $groups */
    public static array $groups = [];

    const DEFAULT_GROUP = "Player";

    const PREFIX = "§c§lGroupSystem §r§f§7";

	public static array $Skins = [];

    public function __construct()
    {
        self::$instance = $this;
        self::$playerApi = new PlayerAPI();
        $this->registerListeners();
        $this->onGroups();

        self::$playerApi->importColorGroups();

        Server::getInstance()->getCommandMap()->register("group", new GroupCommand());
		Server::getInstance()->getCommandMap()->register("nick", new NickCommand());
		Server::getInstance()->getCommandMap()->register("nicklist", new NicklistCommand());
    }

    public function onGroups(){
        foreach (self::getGroupConfig()->getAll() as $group => $value){
            self::$groups[$group] = new Group($group, $value["nametag"], $value["chatformat"], $value["color"], $value["permissions"], $value["inheritance"]);
        }
    }

    /**
     * @return GroupSystem
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    public static function getGroupConfig(): Config {
        return new Config("/home/cloud/data/groupsystem/groups.yml", 2);
    }

    public function registerListeners() {
        $listeners = [
            new PlayerJoinListener(),
            new PlayerChatListener(),
            new PlayerLoginListener(),
            new PlayerQuitListener()
        ];
        foreach ($listeners as $listener){
            Server::getInstance()->getPluginManager()->registerEvents(new $listener(), $this->getPlugin());
        }
    }

    /**
     * @return PlayerAPI
     */
    public static function getPlayerAPI(): PlayerAPI
    {
        return self::$playerApi;
    }
}