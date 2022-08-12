<?php

namespace xxFLORII\Cores;

use battleoase\battlecore\BattleCore;
use battleoase\bedwars\BedWars;
use ceepkev77\cloudbridge\network\DataPacket;
use ceepkev77\cloudbridge\network\packet\UpdateGameServerInfoPacket;
use muqsit\invmenu\type\graphic\network\MultiInvMenuGraphicNetworkTranslator;
use pocketmine\block\BlockLegacyIds;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use xxFLORII\Cores\API\CoresAPI;
use xxFLORII\Cores\Commands\SetupCommand;
use xxFLORII\Cores\Commands\StartCommand;
use xxFLORII\Cores\Listener\BreakListener;
use xxFLORII\Cores\Listener\DamageListener;
use xxFLORII\Cores\Listener\DropListener;
use xxFLORII\Cores\Listener\InteractListener;
use xxFLORII\Cores\Listener\PlaceListener;
use xxFLORII\Cores\Listener\PlayerJoinListener;
use xxFLORII\Cores\Listener\QuitListener;
use xxFLORII\Cores\Listener\TransactionListener;
use xxFLORII\Cores\Tasks\GameTask;

class Main extends PluginBase {

    /** @var int */
    public int $mode = 0;

    /** @var array */
    public static array $redTeam = [];
    public static array $blueTeam = [];
    public static array $teams = ["red", "blue"];
    public static array $breakableBlocks = [BlockLegacyIds::WOOD, BlockLegacyIds::DIAMOND_BLOCK, BlockLegacyIds::TALL_GRASS, BlockLegacyIds::WOODEN_PLANKS];

    /** @var string */
    public static ?string $winnerTeam = null;
    public static string $prefix = "§bCores§r §r§f§8×";

    /** @var self */
    protected static Main $instance;

    public static array $worlds = [];
	public static array $placedBlocks = [];

	const MAX_PLAYERS_PER_TEAM = 2;

    public function onEnable(): void
    {
        self::$instance = $this;

		Main::getInstance()->getScheduler()->scheduleDelayedTask(new class() extends Task{
			public function onRun(): void
			{
				$packet = new UpdateGameServerInfoPacket();
				$packet->type = $packet->TYPE_UPDATE_STATE_MODE;
				$packet->value = 0;
				$packet->sendPacket();
			}
		}, 30);


        @mkdir($this->getDataFolder());
        @mkdir($this->getDataFolder() . "/players");
        @mkdir($this->getDataFolder() . "/maps");

        BattleCore::getInstance()->statsSystem->createStatsTable("Cores");

        $this->saveDefaultConfig();

        $config = self::getInstance()->getConfig();
        $config->set("Arena", "Sakura");
        $config->set("ingame", false);
        $config->set("state", false);
        $config->set("reset", false);
        $config->set("rtime", 10);
        $config->set("time", 20);
        $config->set("playtime", 3600);
        $config->set("block1", true);
        $config->set("block2", true);
        $config->set("block3", true);
        $config->set("block4", true);
        $config->save();

        $this->registerCommands();
        $this->registerListener();
        $this->registerTasks();
    }

    /**
     * Function getWorlds
     * @return bool
     */
    public function getWorlds(): bool{
        self::$worlds = [];

        $servers = array_diff(scandir($this->getDataFolder() . "/maps/"), [".",".."]);
        $servers = array_values($servers);

        if (!isset($servers[0])) {
            return false;
        }
        foreach ($servers as $serverFileName) {
            self::$worlds[] = $serverFileName;
        }
        return true;
    }

    /**
     * Function randomArena
     * @return null|string
     */
    public function randomArena(): ?string{
        if (count(self::$worlds) >= 1) {
            $rand = self::$worlds[array_rand(self::$worlds)];
            if ($rand === "" or $rand === "false" or $rand === false or $rand === null) {
                $this->randomArena();
                return null;
            }
            $this->getLogger()->info("§aThe arena §e" . $rand . "§a will be used§7!");
            return $rand;
        } else {
            $this->getLogger()->warning("§cNo arenas found.");
        }
        return null;
    }

    /**
     * @return void
     */
    public function registerCommands(): void{
        $map = Server::getInstance()->getCommandMap();
        $map->registerAll("CORES",[
            new SetupCommand(),
            new StartCommand(),
        ]);
    }

    /**
     * @return void
     */
    public function registerListener(): void{
        $pm = Server::getInstance()->getPluginManager();
        $pm->registerEvents(new BreakListener(), $this);
        $pm->registerEvents(new DamageListener(), $this);
        $pm->registerEvents(new DropListener(), $this);
        $pm->registerEvents(new InteractListener(), $this);
        $pm->registerEvents(new PlaceListener(), $this);
        $pm->registerEvents(new PlayerJoinListener(), $this);
        $pm->registerEvents(new QuitListener(), $this);
        $pm->registerEvents(new TransactionListener(), $this);
    }

    /**
     * @return void
     */
    public function registerTasks(): void{
        $this->getScheduler()->scheduleRepeatingTask(new GameTask(), 20);
    }

    /**
     * @return self
     */
    public static function getInstance(): self{
        return self::$instance;
    }

    /**
     * @return CoresAPI
     */
    public static function getCoresAPI(): CoresAPI{
        return new CoresAPI();
    }

    /**
     * @return string
     */
    public static function getPrefix(): string{
        return self::$prefix;
    }
}
