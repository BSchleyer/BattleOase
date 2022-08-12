<?php

namespace Cloud;

use Cloud\api\NotifyAPI;
use Cloud\command\CommandManager;
use Cloud\console\CloudConsole;
use Cloud\event\cloud\CloudStartedEvent;
use Cloud\event\EventManager;
use Cloud\lib\snooze\SleeperHandler;
use Cloud\lib\snooze\SleeperNotifier;
use Cloud\network\CloudSocket;
use Cloud\network\protocol\packet\DisconnectPacket;
use Cloud\network\utils\Address;
use Cloud\player\PlayerManager;
use Cloud\scheduler\TaskScheduler;
use Cloud\server\ServerManager;
use Cloud\server\task\CheckServerAmountTask;
use Cloud\template\TemplateManager;
use Cloud\utils\CloudLogger;
use Cloud\utils\Config;
use Cloud\utils\Utils;

class Cloud {

    const METHOD_TMUX = 0;
    const METHOD_SCREEN = 1;
    const METHOD_PROCESS = 2;

    private static self $instance;
    private CloudLogger $logger;
    private CommandManager $commandManager;
    private CloudConsole $cloudConsole;
    private TaskScheduler $taskScheduler;
    private SleeperHandler $sleeperHandler;
    private EventManager $eventManager;
    private CloudSocket $socket;
    private ServerManager $serverManager;
    private TemplateManager $templateManager;
    private PlayerManager $playerManager;
    private NotifyAPI $notifyApi;
    private Config $cloudConfig;

    private float|int $startTime;
    private string $cloudPath;
    private int $socketPort;
    private bool $debugMode;
    private int $startMethod = self::METHOD_PROCESS;
    private string $proxyMotd;

    public function __construct(string $cloudPath) {
        self::$instance = $this;
        $this->cloudPath = $cloudPath;
        $this->registerAutoload();

        if (file_exists(CLOUD_PATH . "temp/servers/")) {
            Utils::deleteDir(CLOUD_PATH . "temp/servers/");
            mkdir(CLOUD_PATH . "temp/servers/");
        }

        $this->startTime = microtime(true);

        $this->logger = new CloudLogger();
        CloudLogger::getInstance()->info("Starting cloud at §e" . date("Y-m-d H:i:s") . "§r...");

        $this->commandManager = new CommandManager();
        $this->taskScheduler = new TaskScheduler();
        $this->eventManager = new EventManager();
        $this->cloudConfig = new Config(CLOUD_PATH . "local/config.json", 1);
        $this->serverManager = new ServerManager();
        $this->templateManager = new TemplateManager();
        $this->playerManager = new PlayerManager();
        $this->sleeperHandler = new SleeperHandler();
        $consoleNotifier = new SleeperNotifier();
        $consoleBuffer = new \Threaded();
        $this->cloudConsole = new CloudConsole($consoleBuffer, $consoleNotifier);

        $this->sleeperHandler->addNotifier($consoleNotifier, function () use($consoleBuffer): void {
            while (($line = $consoleBuffer->shift()) !== null) $this->commandManager->handleInput($line);
        });
        $this->cloudConsole->start(PTHREADS_INHERIT_NONE);


        CloudLogger::getInstance()->info("Check the server versions...");
        if (!Utils::hasDownloaded(Utils::VERSION_POCKETMINE)) {
            CloudLogger::getInstance()->info("Download the server version §b" . Utils::getVersionInfo(Utils::VERSION_POCKETMINE)["Name"] . "§r...");
            Utils::downloadVersion(Utils::VERSION_POCKETMINE);
            CloudLogger::getInstance()->info("§aSuccessfully §rdownloaded the server version §b" . Utils::getVersionInfo(Utils::VERSION_POCKETMINE)["Name"] . "§r!");
        }

        if (!Utils::hasDownloaded(Utils::VERSION_WATERDOGPE)) {
            CloudLogger::getInstance()->info("Download the server version §b" . Utils::getVersionInfo(Utils::VERSION_WATERDOGPE)["Name"] . "§r...");
            Utils::downloadVersion(Utils::VERSION_WATERDOGPE);
            CloudLogger::getInstance()->info("§aSuccessfully §rdownloaded the server version §b" . Utils::getVersionInfo(Utils::VERSION_WATERDOGPE)["Name"] . "§r!");
        }

        CloudLogger::getInstance()->info("Check the plugins...");
        if (!Utils::hasPluginDownloaded(Utils::PLUGIN_CLOUDBRIDGE_PM)) {
            CloudLogger::getInstance()->info("Download the plugin §b" . Utils::getPluginInfo(Utils::PLUGIN_CLOUDBRIDGE_PM)["Name"] . "§r...");
            Utils::downloadPlugin(Utils::PLUGIN_CLOUDBRIDGE_PM);
            CloudLogger::getInstance()->info("§aSuccessfully §rdownloaded the plugin §b" . Utils::getPluginInfo(Utils::PLUGIN_CLOUDBRIDGE_PM)["Name"] . "§r!");
        }

        if (!Utils::hasPluginDownloaded(Utils::PLUGIN_CLOUDBRIDGE_WD)) {
            CloudLogger::getInstance()->info("Download the plugin §b" . Utils::getPluginInfo(Utils::PLUGIN_CLOUDBRIDGE_WD)["Name"] . "§r...");
            Utils::downloadPlugin(Utils::PLUGIN_CLOUDBRIDGE_WD);
            CloudLogger::getInstance()->info("§aSuccessfully §rdownloaded the plugin §b" . Utils::getPluginInfo(Utils::PLUGIN_CLOUDBRIDGE_WD)["Name"] . "§r!");
        }

        if (!Utils::hasPluginDownloaded(Utils::PLUGIN_JOINHANDLER_WD)) {
            CloudLogger::getInstance()->info("Download the plugin §b" . Utils::getPluginInfo(Utils::PLUGIN_JOINHANDLER_WD)["Name"] . "§r...");
            Utils::downloadPlugin(Utils::PLUGIN_JOINHANDLER_WD);
            CloudLogger::getInstance()->info("§aSuccessfully §rdownloaded the plugin §b" . Utils::getPluginInfo(Utils::PLUGIN_JOINHANDLER_WD)["Name"] . "§r!");
        }

        if (!$this->cloudConfig->exists("cloud-port")) $this->cloudConfig->set("cloud-port", mt_rand(600, 56486));
        if (!$this->cloudConfig->exists("debug-mode")) $this->cloudConfig->set("debug-mode", false);
        if (!$this->cloudConfig->exists("proxy-motd")) $this->cloudConfig->set("proxy-motd", "§c{server}");
        if (PHP_OS_FAMILY == "Linux") {
            if (!$this->cloudConfig->exists("start-method")) $this->cloudConfig->set("start-method", "tmux");
            if (!$this->cloudConfig->exists("start-methods")) $this->cloudConfig->set("start-methods", "tmux:screen:process");
            else if ($this->cloudConfig->get("start-methods") !== "tmux:screen:process") $this->cloudConfig->set("start-methods", "tmux:screen:process");
        } else {
            if ($this->cloudConfig->exists("start-method")) $this->cloudConfig->remove("start-method");
            if ($this->cloudConfig->exists("start-methods")) $this->cloudConfig->remove("start-methods");
        }

        $this->cloudConfig->save();
        $this->cloudConfig->reload();
        $this->socketPort = $this->cloudConfig->get("cloud-port");
        $this->debugMode = $this->cloudConfig->get("debug-mode");
        $this->startMethod = ($this->cloudConfig->exists("start-method") ? $this->startMethodStringToInteger($this->cloudConfig->get("start-method")) : self::METHOD_PROCESS);
        $this->proxyMotd = $this->cloudConfig->get("proxy-motd");

        $this->socket = new CloudSocket(new Address("127.0.0.1", $this->socketPort));

        if ($this->startMethod == self::METHOD_TMUX) {
            if (!Utils::isTmuxInstalled()) {
                CloudLogger::getInstance()->error("The start method for servers §eTMUX §rdoesn't exists!");
                CloudLogger::getInstance()->info("Install it with §b\"apt-get install tmux\"§r!");
                $this->shutdown();
            }
        } else if ($this->startMethod == self::METHOD_SCREEN) {
            if (!Utils::isScreenInstalled()) {
                CloudLogger::getInstance()->error("The start method for servers §eSCREEN §rdoesn't exists!");
                CloudLogger::getInstance()->info("Install it with §b\"apt-get install screen\"§r!");
                $this->shutdown();
            }
        }

        $this->notifyApi = new NotifyAPI();

        $this->templateManager->loadTemplates();
        $this->taskScheduler->scheduleTask(new CheckServerAmountTask());

        CloudLogger::getInstance()->info("Cloud was §astarted §rin §e" . round((microtime(true) - $this->startTime), 2) . "s§r!");
        CloudLogger::getInstance()->info("Type §e\"help\"§r, to get a list of all commands!");
        $this->getEventManager()->callEvent(new CloudStartedEvent());
        $this->tick();
    }

    public function registerAutoload() {
        spl_autoload_register(function($class) {
            if (substr($class, 0, strlen("Cloud\\")) === "Cloud\\") {
                $file = __DIR__ . DIRECTORY_SEPARATOR . str_replace(["\\", "\\\\", "/", "//"], DIRECTORY_SEPARATOR, str_replace("Cloud\\", "", $class)) . ".php";
            } else {
                $file = __DIR__ . str_replace(["\\", "\\\\", "/", "//"], DIRECTORY_SEPARATOR, $class) . ".php";
            }
            if (!class_exists($class) and file_exists($file)) require_once $file;
        });
    }

    public function shutdown() {
        CloudSocket::getInstance()->broadcastPacket(DisconnectPacket::create(DisconnectPacket::CLOUD_SHUTDOWN));
        $this->taskScheduler->cancelAll();
        $this->cloudConsole->shutdown();
        $this->socket->getUdpServer()->close();
        $this->serverManager->stopAll(true);
        $this->logger->close();
        @Utils::kill(getmypid());
        exit(1);
    }

    private function tick() {
        $start = microtime(true);
        while (true) {
            $this->sleeperHandler->sleepUntil($start);

            usleep(50 * 1000);
            $this->taskScheduler->onUpdate();
        }
    }

    public function getLogger(): CloudLogger {
        return $this->logger;
    }

    public function getCommandManager(): CommandManager {
        return $this->commandManager;
    }

    public function getCloudConsole(): CloudConsole {
        return $this->cloudConsole;
    }

    public function getSleeperHandler(): SleeperHandler {
        return $this->sleeperHandler;
    }

    public function getTaskScheduler(): TaskScheduler {
        return $this->taskScheduler;
    }

    public function getEventManager(): EventManager {
        return $this->eventManager;
    }

    public function getCloudPath(): string {
        return $this->cloudPath;
    }

    public function getSocketPort(): int {
        return $this->socketPort;
    }

    public function getProxyMotd(): string {
        return $this->proxyMotd;
    }

    public function getCloudConfig(): Config {
        return $this->cloudConfig;
    }

    public function isDebugModeEnabled(): bool {
        return $this->debugMode;
    }

    public function getStartTime(): float|int {
        return $this->startTime;
    }

    public function getSocket(): CloudSocket {
        return $this->socket;
    }

    public function getPlayerManager(): PlayerManager {
        return $this->playerManager;
    }

    public function getServerManager(): ServerManager {
        return $this->serverManager;
    }

    public function getStartMethod(): int {
        return $this->startMethod;
    }

    public function getTemplateManager(): TemplateManager {
        return $this->templateManager;
    }

    public function getNotifyApi(): NotifyAPI {
        return $this->notifyApi;
    }

    public function startMethodStringToInteger(string $method): int {
        if ($method == "screen") return self::METHOD_SCREEN;
        else if ($method == "tmux") return self::METHOD_TMUX;
        else if ($method == "process") return self::METHOD_PROCESS;
        return self::METHOD_PROCESS;
    }

    public static function getInstance(): Cloud {
        return self::$instance;
    }
}