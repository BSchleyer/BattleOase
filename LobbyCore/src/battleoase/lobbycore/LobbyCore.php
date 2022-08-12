<?php


namespace battleoase\lobbycore;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\BattlePlayer;
use battleoase\battlecore\groupSystem\GroupSystem;
use battleoase\battlecore\groupSystem\api\PlayerAPI;
use battleoase\battlecore\npcSystem\entities\NPCEntity;
use battleoase\battlecore\npcSystem\NpcSystem;
use battleoase\battlecore\npcSystem\utils\Emotes;
use battleoase\lobbycore\bridge\Bridge;

use battleoase\lobbycore\commands\BuildCommand;
use battleoase\lobbycore\commands\DailyRewardCommand;
use battleoase\lobbycore\commands\FlyCommand;
use battleoase\lobbycore\commands\IslandCommand;
use battleoase\lobbycore\commands\LobbyGamesCommand;
use battleoase\lobbycore\commands\MessageCommand;
use battleoase\lobbycore\commands\SoonCommand;
use battleoase\lobbycore\commands\SpawnHologram;
use battleoase\lobbycore\commands\XyzCommand;

use battleoase\lobbycore\events\DataPacketReceiveListener;
use battleoase\lobbycore\events\PlayerInteractListener;
use battleoase\lobbycore\events\PlayerJoinListener;
use battleoase\lobbycore\events\PlayerMoveListener;
use battleoase\lobbycore\events\PlayerQuitListener;
use battleoase\lobbycore\events\ProxyPlayerJoinListener;
use battleoase\lobbycore\events\SecurityPlayerEvents;
use battleoase\lobbycore\eventSystem\EventSystem;
use battleoase\lobbycore\fix\FormIconFix;
use battleoase\lobbycore\jumpAndRun\JumpAndRun;
use battleoase\lobbycore\player\PlayerManager;

use battleoase\lobbycore\provider\FloatingTextsProvider;
use battleoase\lobbycore\task\CheckDropTask;
use battleoase\lobbycore\task\UpdateScoreTask;
use battleoase\lobbycore\utils\Hologram;
use battleoase\lobbycore\utils\ScoreTrait;
use battleoase\lobbycore\utils\SettingUtils;
use ceepkev77\BattleCore\provider\AsyncExecutor;
use ceepkev77\cloudapi\CloudAPI;
use ceepkev77\cloudbridge\objects\GameServer;
use ceepkev77\lobbyapi\LobbyAPI;
use FG\ASN1\Identifier;
use pocketmine\entity\Location;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\AsyncTask;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\particle\FloatingTextParticle;

class LobbyCore extends PluginBase
{
    use ScoreTrait;

    public array $protection = [];
    public array $doublejump = [];
    public array $fly = [];


    const PREFIX = TextFormat::AQUA . "Lobby " . TextFormat::DARK_GRAY . "§8× " . TextFormat::GRAY;

    private static LobbyCore $instance;
    private static PlayerManager $playerManager;

    private JumpAndRun $jump;
    private Bridge $bridge;

    public function onEnable(): void
    {
        Server::getInstance()->getWorldManager()->getDefaultWorld()->setTime(1000);
        Server::getInstance()->getWorldManager()->getDefaultWorld()->stopTime();

        self::$instance = $this;
        self::$playerManager = new PlayerManager();

        $this->jump = new JumpAndRun();
        $this->bridge = new Bridge();

        $this->getScheduler()->scheduleRepeatingTask(new UpdateScoreTask(), 40);
        //$this->getScheduler()->scheduleRepeatingTask(new CheckDropTask(), 20);

        $this->getServer()->getCommandMap()->register("build", new BuildCommand());
        $this->getServer()->getCommandMap()->register("lobbygames", new LobbyGamesCommand());
        $this->getServer()->getCommandMap()->register("xyz", new XyzCommand());
        $this->getServer()->getCommandMap()->register("fly", new FlyCommand());
        $this->getServer()->getCommandMap()->register("island", new IslandCommand());
        $this->getServer()->getCommandMap()->register("spawnholo", new SpawnHologram());

        $this->getServer()->getCommandMap()->register("dailyreward", new DailyRewardCommand());
        $this->getServer()->getCommandMap()->register("soon", new SoonCommand());
        $this->registerListeners();
        (new FloatingTextsProvider())->init();
        $this->initNPCS();

    }

    public function initNPCS(): void {
        NpcSystem::spawn("BATTLEUNITY_bedwarsV2", "§r§l•§c● §l§cBed§r§lWars §r§f§c●§r§l• ", new Location(-42751.5, 48, -5916.5, Server::getInstance()->getWorldManager()->getDefaultWorld(), 0, 0), true, function(Player $player) {
            Server::getInstance()->dispatchCommand($player, "games bedwars");
        });


    }

    public function registerListeners()
    {
        $listeners = [
            new PlayerJoinListener(),
            new PlayerInteractListener(),
            new SecurityPlayerEvents(),
            new PlayerMoveListener(),
            //new ProxyPlayerJoinListener(),
            new FormIconFix(),
            new PlayerQuitListener()
        ];
        foreach ($listeners as $listener) {
            Server::getInstance()->getPluginManager()->registerEvents(new $listener(), $this);
        }
    }

    /**
     * @return LobbyCore
     */
    public static function getInstance(): LobbyCore
    {
        return self::$instance;
    }

    /**
     * @return JumpAndRun
     */
    public function getJumpAndRun(): JumpAndRun
    {
        return $this->jump;
    }

    /**
     * @return Bridge
     */
    public function getBridge(): Bridge
    {
        return $this->bridge;
    }

    /**
     * @return PlayerManager
     */
    public static function getPlayerManager(): PlayerManager
    {
        return self::$playerManager;
    }

    public function getCoins(Player $player){
        $name = $player->getName();
        $result = BattleCore::$connection->query("SELECT * FROM Core.players WHERE player_name='$name'");
        if ($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                return $row["coins"];
            }
        }
    }

    public function scoreboard(BattlePlayer $player, bool $msg = FALSE)
    {

        //$coins = $battleplayer->getPlayerInfo("coins");
        $coins = LobbyCore::getInstance()->getCoins($player);

        $group = GroupSystem::getPlayerAPI()->getGroup($player);

        $color = "§7";
        if(isset(GroupSystem::$groups[$group])) {
            $color = GroupSystem::$groups[$group]->getColor();
        }

        $online = 0;

        $onlinetime = $this->getServer()->getPluginManager()->getPlugin("OnlineTime")->getRealTime($player);

        $this->createScoreboard($player, " §3•§b● §b§lBattleOase.NET §b●§3•","lobby");
        $this->addLine($player, 0, "§l§1", "lobby");
        $this->addLine($player, 1, "§8● §7Rank", "lobby");
        $this->addLine($player, 2, "§8  {$color}$group", "lobby");
        $this->addLine($player, 3, "§l§2", "lobby");
        $this->addLine($player, 4, "§8● §7Coins", "lobby");
        $this->addLine($player, 5, "§8  §e" . $coins, "lobby");
        $this->addLine($player, 6, "§l§3", "lobby");
        $this->addLine($player, 7, "§8● §7Onlinetime", "lobby");
        $this->addLine($player, 8, "§8  §e" . $onlinetime, "lobby");
        $this->addLine($player, 9, "§l§4", "lobby");
        $this->addLine($player, 10, "§8● §7Online:", "lobby");
        $this->addLine($player, 11, "§8  §e" . $online . "§7/§e" . "100", "lobby");
    }

    public function getFile(): string
    {
        return parent::getFile(); // TODO: Change the autogenerated stub
    }

    public static function updateLobbyScoreboard()
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $players) {
        	if ($players instanceof BattlePlayer){
				LobbyCore::getInstance()->removeScoreboard($players, "lobby");
				LobbyCore::getInstance()->scoreboard($players);}
        }
    }
}