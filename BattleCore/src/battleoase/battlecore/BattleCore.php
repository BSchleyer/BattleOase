<?php

namespace battleoase\battlecore;

use battleoase\battlecore\coinSystem\CoinSystem;
use battleoase\battlecore\npcSystem\NpcSystem;
use battleoase\battlecore\player\BattlePlayer;
use battleoase\battlecore\player\provider\PlayersProvider;
use battleoase\battlecore\statsSystem\StatsSystem;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class BattleCore extends PluginBase implements Listener {

    /** @var BattleCore $instance */
    private static BattleCore $instance;
    /** @var \mysqli $connection */
    private static $connection;

    /** @var CoinSystem $coinSystem */
    public CoinSystem $coinSystem;
    /** @var StatsSystem $statsSystem */
    public StatsSystem $statsSystem;
    /** @var NpcSystem $npcSystem */
    public NpcSystem $npcSystem;

    protected function onLoad(): void {
        self::$instance = $this;
        $config = new Config("/home/cloud/mysql.yml", Config::YAML, ["address" => "", "user" => "", "password" => ""]);
        self::$connection = mysqli_connect($config->get("address"), $config->get("user"), $config->get("password"));
        PlayersProvider::init();
    }

    protected function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->coinSystem = new CoinSystem();
        $this->statsSystem = new StatsSystem();
        $this->npcSystem = new NpcSystem();
    }

    public function onPlayerCreation(PlayerCreationEvent $event) {
        $event->setPlayerClass(BattlePlayer::class);
    }

    /**
     * @return false|\mysqli
     */
    public function getConnection(): bool|\mysqli {
        return self::$connection;
    }

    /**
     * @return BattleCore
     */
    public static function getInstance(): BattleCore {
        return self::$instance;
    }

}