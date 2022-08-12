<?php

namespace ceepkev77\cloudbridge;

use battleoase\battlecore\discordSystem\DiscordManager;
use ceepkev77\cloudbridge\command\CloudCommand;
use ceepkev77\cloudbridge\command\HubCommand;
use ceepkev77\cloudbridge\command\PermissionCommand;
use ceepkev77\cloudbridge\command\RegPlayersCommand;
use ceepkev77\cloudbridge\command\SaveCommand;
use ceepkev77\cloudbridge\command\ServerInfoCommand;
use ceepkev77\cloudbridge\crashLoggerSystem\utils\CrashDumpReader;
use ceepkev77\cloudbridge\crashLoggerSystem\utils\DiscordHandler;
use ceepkev77\cloudbridge\listener\server\PlayerJoinListener;
use ceepkev77\cloudbridge\listener\server\PlayerQuitListener;
use ceepkev77\cloudbridge\network\DataPacket;
use ceepkev77\cloudbridge\network\handler\PacketHandler;
use ceepkev77\cloudbridge\network\handler\RequestHandler;
use ceepkev77\cloudbridge\network\packet\AddPlayerToCWQueuePacket;
use ceepkev77\cloudbridge\network\packet\CloudPlayerAddPermissionPacket;
use ceepkev77\cloudbridge\network\packet\GameServerConnectPacket;
use ceepkev77\cloudbridge\network\packet\GameServerDisconnectPacket;
use ceepkev77\cloudbridge\network\packet\GameServerInfoRequestPacket;
use ceepkev77\cloudbridge\network\packet\GameServerInfoResponsePacket;
use ceepkev77\cloudbridge\network\packet\KeepALivePacket;
use ceepkev77\cloudbridge\network\packet\ListCloudPlayersRequestPacket;
use ceepkev77\cloudbridge\network\packet\ListCloudPlayersResponsePacket;
use ceepkev77\cloudbridge\network\packet\ListServerRequestPacket;
use ceepkev77\cloudbridge\network\packet\ListServerResponsePacket;
use ceepkev77\cloudbridge\network\packet\PlayerKickPacket;
use ceepkev77\cloudbridge\network\packet\PlayerMessagePacket;
use ceepkev77\cloudbridge\network\packet\PlayerMovePacket;
use ceepkev77\cloudbridge\network\packet\ProxyPlayerJoinPacket;
use ceepkev77\cloudbridge\network\packet\ProxyPlayerQuitPacket;
use ceepkev77\cloudbridge\network\packet\StartGroupPacket;
use ceepkev77\cloudbridge\network\packet\StartServerPacket;
use ceepkev77\cloudbridge\network\packet\StopGroupPacket;
use ceepkev77\cloudbridge\network\packet\StopServerPacket;
use ceepkev77\cloudbridge\network\packet\VersionInfoPacket;
use ceepkev77\cloudbridge\network\task\RequestTask;
use ceepkev77\cloudbridge\objects\CloudGroup;
use ceepkev77\cloudbridge\objects\GameServer;
use ceepkev77\cloudbridge\objects\VersionInfo;
use ceepkev77\lobbyapi\LobbyAPI;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\Process;

class CloudBridge extends PluginBase
{

    public static array $requests = [];

    private static RequestHandler $requestHandler;

    public static ?Config $config;

    private static CloudBridge $instance;
    public static array $qeueuPlayer = [];
    private static GameServer $gameServer;
    public static VersionInfo $versionInfo;

    public array $queue = [];


    public function onLoad(): void
    {
        self::$instance = $this;
        $this->getScheduler()->scheduleRepeatingTask(new RequestTask(), 1);
    }

    public function onEnable(): void
    {
        self::$requestHandler = new RequestHandler();
        self::$versionInfo = new VersionInfo("Cloud", "[]", "0.0.0", "NOT FOUND");
        $this->checkOldCrashDumps();
        self::registerPackets();
        $this->getServer()->getPluginManager()->registerEvents(new PlayerJoinListener(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new PlayerQuitListener(), $this);
        $this->getServer()->getCommandMap()->register("command:battlecloud", new CloudCommand());
        $this->getServer()->getCommandMap()->register("command:battlecloud", new SaveCommand());
        $this->getServer()->getCommandMap()->register("command:battlecloud", new ServerInfoCommand());
        $this->getServer()->getCommandMap()->register("command:battlecloud", new PermissionCommand());
		$this->getServer()->getCommandMap()->register("command:battlecloud", new RegPlayersCommand());
        $this->getServer()->getCommandMap()->register("command:battlecloud", new HubCommand());
        $pk = new GameServerConnectPacket();
        $pk->addValue("serverPort", $this->getServer()->getPort());
        $pk->addValue("serverPid", getmypid());
        $pk->sendPacket();
        $serverInfoPacket = new GameServerInfoRequestPacket();
        $serverInfoPacket->server = Server::getInstance()->getMotd();
        $serverInfoPacket->submitRequest($serverInfoPacket, function (DataPacket $dataPacket) {
            if($dataPacket instanceof GameServerInfoResponsePacket) {
                $gameServer = new GameServer($dataPacket->getServerInfoName(), new CloudGroup($dataPacket->getTemplateName(), $dataPacket->isMaintenance(), $dataPacket->isBeta(), $dataPacket->isLobby(), $dataPacket->getMaxPlayer()));
                $gameServer->setState($dataPacket->getState());
                $gameServer->setIsPrivate($dataPacket->isPrivate());
                $gameServer->setPlayerCount($dataPacket->getPlayerCount());
                self::$gameServer = $gameServer;
            }
        });

    }

    public function onDisable(): void
    {
        $pk = new GameServerDisconnectPacket();
        $pk->sendPacket();
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            $player->kick("Server was closed");
        }

     //   $this->checkNewCrashDump();
    }

    /**
     * @return GameServer
     */
    public static function getGameServer(): GameServer
    {
        return self::$gameServer;
    }

    /**
     * @return VersionInfo
     */
    public static function getVersionInfo(): VersionInfo
    {
        return self::$versionInfo;
    }


    private static function registerPackets()
    {
        $packets = [
            GameServerConnectPacket::class,
            GameServerDisconnectPacket::class,
            GameServerInfoRequestPacket::class,
            GameServerInfoResponsePacket::class,
            ListServerRequestPacket::class,
            ListServerResponsePacket::class,
            ProxyPlayerJoinPacket::class,
            ProxyPlayerQuitPacket::class,
            KeepALivePacket::class,
            StartGroupPacket::class,
            StartServerPacket::class,
            StopGroupPacket::class,
            StopServerPacket::class,
            CloudPlayerAddPermissionPacket::class,
            VersionInfoPacket::class,
            PlayerMovePacket::class,
            ListCloudPlayersRequestPacket::class,
            ListCloudPlayersResponsePacket::class,
			PlayerMessagePacket::class,
            AddPlayerToCWQueuePacket::class,
			PlayerKickPacket::class
        ];

        foreach ($packets as $packet) {
            $reflection = new \ReflectionClass($packet);
            PacketHandler::registerPacket($reflection->getShortName(), $packet);
        }
    }

    /**
     * @return RequestHandler
     */
    public static function getRequestHandler(): RequestHandler
    {
        return self::$requestHandler;
    }

    public function getConfig(): Config
    {
        return parent::getConfig();
    }

    /**
     * @return CloudBridge
     */
    public static function getInstance(): CloudBridge
    {
        return self::$instance;
    }

    public static function getPrefix(): string
    {
        return "§l§cBattle§4Cloud§r §8× ";
    }


    public function getTemplate(): string {
        return $this->getServerProperties()->get("template");
    }

    public function getCloudPort(): int {
        return $this->getServerProperties()->get("cloud-port");
    }

    public function getCloudPassword(): string {
        return $this->getServerProperties()->get("cloud-password");
    }

    public function getServerProperties(): Config {
        return new Config(Server::getInstance()->getDataPath() . "server.properties", 0);
    }


    public function checkOldCrashDumps(): void{
        $validityDuration = 24 * 60 * 60;
        $delete = false;

        $files = $this->getCrashdumpFiles();
        $this->getLogger()->info("Checking old crash dumps (files: ".count($files).")");

        $removed = 0;
        foreach($files as $filePath){
            try{
                $crashDumpReader = new CrashDumpReader($filePath);

                if(!$crashDumpReader->hasRead()){
                    continue;
                }

                if($delete === true and time() - $crashDumpReader->getCreationTime() >= $validityDuration){
                    unlink($filePath);
                    ++$removed;
                }
            }catch(\Throwable $e){
                $this->getLogger()->warning("Error during file check of \"".basename($filePath)."\": ".$e->getMessage()." in file ".$e->getFile()." on line ".$e->getLine());
                foreach(explode("\n", $e->getTraceAsString()) as $traceString){
                    $this->getLogger()->debug("[ERROR] ".$traceString);
                }
            }
        }

        $fileAmount = count($files);
        $percentage = $fileAmount > 0 ? round($removed * 100 / $fileAmount, 2) : "NAN";

        $message = "Checks finished, Deleted crash dump files: ".$removed." (".$percentage."%)";
        if($removed > 0){
            $this->getLogger()->notice($message);
        }else{
            $this->getLogger()->info($message);
        }
    }

    public function checkNewCrashDump(): void{
        $this->getLogger()->debug("Checking for new crash dump");
        $files = $this->getCrashdumpFiles();

        $startTime = (int) $this->getServer()->getStartTime();
        foreach($files as $filePath){
            try{
                $crashDumpReader = new CrashDumpReader($filePath);

                if(!$crashDumpReader->hasRead() or $crashDumpReader->getCreationTime() < $startTime){
                    continue;
                }

                $this->getLogger()->notice("New crash dump found. Sending now.");
                $this->reportCrashDump($crashDumpReader);
            }catch(\Throwable $e){
                $this->getLogger()->warning("Error while checking potentially new crash dump \"".basename($filePath)."\": ".$e->getMessage()." in file ".$e->getFile()." on line ".$e->getLine());
                foreach(explode("\n", $e->getTraceAsString()) as $traceString){
                    $this->getLogger()->debug("[ERROR] ".$traceString);
                }
            }
        }

        $this->getLogger()->debug("Checks finished");

    }

    public function reportCrashDump(CrashDumpReader $crashDumpReader): void{
        if($crashDumpReader->hasRead()){
            $handler = new DiscordHandler("https://discord.com/api/webhooks/939204360146137198/EsFjF_0cO6jY8DBwaHKpZ0Ecz0ZnGDCDBIAmE8oKmhlql4sbXw_ZJD95A4nx-0WQeNcp", $crashDumpReader);
            $handler->announceCrash = true;
            $handler->fullPath = true;
            $handler->dateFormat = "d.m.Y (l): H:i:s [e]";

            $handler->submit();
            $this->getLogger()->debug("Crash dump sent");
        }
    }

    public function getCrashdumpFiles(): array{
        return glob($this->getServer()->getDataPath()."crashdumps/*.log");
    }


}