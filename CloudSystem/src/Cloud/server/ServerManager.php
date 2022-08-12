<?php

namespace Cloud\server;

use Cloud\api\NotifyAPI;
use Cloud\Cloud;
use Cloud\network\CloudSocket;
use Cloud\network\protocol\packet\DisconnectPacket;
use Cloud\network\protocol\packet\DispatchCommandPacket;
use Cloud\network\protocol\packet\RegisterServerPacket;
use Cloud\network\protocol\packet\UnregisterServerPacket;
use Cloud\scheduler\ClosureTask;
use Cloud\scheduler\TaskScheduler;
use Cloud\server\id\IdManager;
use Cloud\server\port\PortManager;
use Cloud\server\status\ServerStatus;
use Cloud\server\task\CheckServerStartupTask;
use Cloud\template\Template;
use Cloud\utils\CloudLogger;
use Cloud\utils\Config;
use Cloud\utils\Utils;

class ServerManager {

    private static self $instance;
    private IdManager $idManager;
    private PortManager $portManager;
    /** @var Server[] */
    private array $servers = [];
    private array $processes = [];

    public function __construct() {
        self::$instance = $this;
        $this->idManager = new IdManager();
        $this->portManager = new PortManager();
    }

    public function startServer(Template $template, int $count = 1) {
        $this->initTemplate($template);
        if (count($this->getServersOfTemplate($template)) >= $template->getMaxServers()) {
            CloudLogger::getInstance()->warning("No servers from the template §e" . $template->getName() . " §rcan be started anymore because the limit was reached!");
        } else {
            for ($i = 0; $i < $count; $i++) {
                if (count($this->getServersOfTemplate($template)) >= $template->getMaxServers()) break;
                $id = $this->getIdManager()->getFreeId($template);
                if ($id !== 0) {
                    $port = ($template->getType() == $template::TYPE_SERVER ? $this->getPortManager()->getFreePort() : $this->getPortManager()->getFreeProxyPort());
                    if ($port !== 0) {
                        $server = new Server($template->getName() . "-" . $id, $id, $port, $template, microtime(true));
                        if (!file_exists($server->getPath())) mkdir($server->getPath());
                        Utils::copyDir($template->getPath(), $server->getPath());
                        if ($template->getType() == $template::TYPE_SERVER) {
                            Utils::copyDir(CLOUD_PATH . "local/plugins/pmmp/", $server->getPath() . "plugins/");
                        } else {
                            Utils::copyDir(CLOUD_PATH . "local/plugins/wdpe/", $server->getPath() . "plugins/");
                        }

                        $server->createProperties();

                        $this->addServer($server);
                        $this->getIdManager()->addId($template, $id);
                        $this->getPortManager()->addPort($port);

                        CloudLogger::getInstance()->info("The server §e" . $server->getName() . " §ris §astarting§r...");
                        NotifyAPI::getInstance()->sendNotify("The server §e" . $server->getName() . " §7is §astarting§7...");
                        TaskScheduler::getInstance()->scheduleTask(new CheckServerStartupTask($server));
                        $this->openProcess($server);
                    }
                }
            }
        }
    }

    public function stopServer(Server $server) {
        CloudLogger::getInstance()->info("The server §e" . $server->getName() . " §ris §cstopping§r...");
        NotifyAPI::getInstance()->sendNotify("The server §e" . $server->getName() . " §7is §cstopping§7...");
        $server->setServerStatus(ServerStatus::STATUS_STOPPING);
        $client = CloudSocket::getInstance()->getClient($server->getName());
        if ($client !== null) CloudSocket::getInstance()->sendPacket(DisconnectPacket::create(DisconnectPacket::SERVER_SHUTDOWN), $client);
        else $this->closeProcess($server);
    }

    public function forceStopServer(Server $server) {
        CloudLogger::getInstance()->info("The server §e" . $server->getName() . " §ris §cstopping§r...");
        NotifyAPI::getInstance()->sendNotify("The server §e" . $server->getName() . " §7is §cstopping§7...");
        $server->setServerStatus(ServerStatus::STATUS_STOPPING);

        $pid = $this->getPid($server);
        if ($pid !== null) Utils::kill($pid);
    }

    public function stopTemplate(Template $template) {
        foreach ($this->getServersOfTemplate($template) as $server) $this->stopServer($server);
    }

    public function stopAll(bool $force = false) {
        foreach ($this->servers as $server) {
            if (!$force) $this->stopServer($server);
            else $this->forceStopServer($server);
        }
    }

    public function saveServer(Server $server) {
        $this->dispatchCommand($server, "save-all");

        if (file_exists($server->getPath())) {
            TaskScheduler::getInstance()->scheduleTask(new ClosureTask(function () use($server): void {
                if ($server->getTemplate()->getType() == Template::TYPE_SERVER) {
                    Utils::copyDir($server->getPath() . "worlds/", $server->getTemplate()->getPath() . "worlds/");
                    Utils::copyDir($server->getPath() . "plugin_data/", $server->getTemplate()->getPath() . "plugin_data/");
                    Utils::copyDir($server->getPath() . "crashdumps/", $server->getTemplate()->getPath() . "crashdumps/");
                    Utils::copyDir($server->getPath() . "players/", $server->getTemplate()->getPath() . "players/");
                    Utils::copyFile($server->getPath() . "ops.txt", $server->getTemplate()->getPath() . "ops.txt");
                    Utils::copyFile($server->getPath() . "white-list.txt", $server->getTemplate()->getPath() . "white-list.txt");
                    Utils::copyFile($server->getPath() . "server.log", $server->getTemplate()->getPath() . "server.log");
                    Utils::copyFile($server->getPath() . "pocketmine.yml", $server->getTemplate()->getPath() . "pocketmine.yml");
                } else {
                    Utils::copyDir($server->getPath() . "logs/", $server->getTemplate()->getPath() . "logs/");
                    Utils::copyDir($server->getPath() . "plugins/", $server->getTemplate()->getPath() . "plugins/");
                    Utils::copyDir($server->getPath() . "packs/", $server->getTemplate()->getPath() . "packs/");
                    Utils::copyFile($server->getPath() . "config.yml", $server->getTemplate()->getPath() . "config.yml");
                    Utils::copyFile($server->getPath() . "lang.ini", $server->getTemplate()->getPath() . "lang.ini");
                }
            }, 30, 0, 1));
        }
    }

    public function isRunning(Server $server): bool {
        return ($server->getLastConnectionCheckTime() + 5) >= microtime(true);
    }

    public function startDefaultServers() {
        foreach (Cloud::getInstance()->getTemplateManager()->getTemplates() as $template) {
            if ($template->isAutoStart()) {
                if ($template->getMinServers() > 0) {
                    if (count($this->getServersOfTemplate($template)) < $template->getMinServers()) {
                        $remaining = $template->getMinServers() - count($this->getServersOfTemplate($template));
                        $this->startServer($template, $remaining);
                    }
                }
            }
        }
    }

    public function removePid(Server $server) {
        if (file_exists($server->getPath() . "pid.txt")) unlink($server->getPath() . "pid.txt");
    }

    public function getPid(Server $server): ?string {
        if (file_exists($server->getPath() . "pid.txt")) intval(file_get_contents($server->getPath() . "pid.txt"));
        return null;
    }

    public function dispatchCommand(Server $server, string $commandLine) {
        $client = CloudSocket::getInstance()->getClient($server->getName());
        if ($client !== null) CloudSocket::getInstance()->sendPacket(DispatchCommandPacket::create($server->getName(), $commandLine), $client);
    }

    public function initTemplate(Template $template) {
        $this->idManager->initTemplate($template);
    }

    public function addServer(Server $server) {
        if (!isset($this->servers[$server->getName()])) $this->servers[$server->getName()] = $server;

        foreach ($this->getServers() as $proxyServer) {
            if ($proxyServer->getTemplate()->getType() == Template::TYPE_PROXY) {
                $client = CloudSocket::getInstance()->getClient($proxyServer->getName());
                if ($client !== null) CloudSocket::getInstance()->sendPacket(RegisterServerPacket::create($server->getName(), $server->getPort()), $client);
            }
        }
    }

    public function removeServer(Server $server) {
        if (isset($this->servers[$server->getName()])) unset($this->servers[$server->getName()]);

        foreach ($this->getServers() as $proxyServer) {
            if ($proxyServer->getTemplate()->getType() == Template::TYPE_PROXY) {
                $client = CloudSocket::getInstance()->getClient($proxyServer->getName());
                if ($client !== null) CloudSocket::getInstance()->sendPacket(UnregisterServerPacket::create($server->getName()), $client);
            }
        }
    }

    public function addAllServersToProxy(Server $proxyServer) {
        foreach ($this->getServers() as $server) {
            if ($server->getTemplate()->getType() == Template::TYPE_SERVER) {
                $client = CloudSocket::getInstance()->getClient($proxyServer->getName());
                if ($client !== null) CloudSocket::getInstance()->sendPacket(RegisterServerPacket::create($server->getName(), $server->getPort()), $client);
            }
        }
    }

    private function openProcess(Server $server) {
        $filePath = $server->getPath() . ($server->getTemplate()->getType() == Template::TYPE_SERVER ? "PocketMine-MP.phar" : "Waterdog.jar");
        $binPath = Utils::getBinary();
        $commandLine = "";
        if ($server->getTemplate()->getType() == Template::TYPE_SERVER) $commandLine = "cd " . $server->getPath() . " && " . $binPath . " " . $filePath . " --no-wizard --data " . $server->getPath();
        else $commandLine = "cd " . $server->getPath() . " && java -jar " . $filePath;

        if (Cloud::getInstance()->getStartMethod() == Cloud::METHOD_TMUX) {
            passthru("tmux new-session -d -s " . $server->getName() . " bash -c '" . $commandLine . "'");
        } else if (Cloud::getInstance()->getStartMethod() == Cloud::METHOD_SCREEN) {
            passthru("screen -dmS " . $server->getName() . " " . $commandLine);
        } else if (Cloud::getInstance()->getStartMethod() == Cloud::METHOD_PROCESS) {
            $spec = [0 => fopen("php://temp", "r"), 1 => fopen("php://temp", "r"), 2 => fopen("php://temp", "r")];

            $proc = proc_open($commandLine, $spec, $pipes);
            $this->processes[$server->getName()] = $proc;
        }
    }

    public function closeProcess(Server $server) {
        if (Cloud::getInstance()->getStartMethod() == Cloud::METHOD_TMUX || Cloud::getInstance()->getStartMethod() == Cloud::METHOD_SCREEN) {
            $pid = $this->getPid($server);
            if ($pid !== null) {
                Utils::kill($pid, true);
            }
        } else if (Cloud::getInstance()->getStartMethod() == Cloud::METHOD_PROCESS) {
            $process = $this->getProcess($server);
            if ($process !== null) {
                unset($this->processes[$server->getName()]);
                @proc_terminate($process);
                @proc_close($process);
            }
        }
    }

    public function getProcess(Server $server): mixed {
        foreach ($this->processes as $serverName => $process) {
            if ($serverName == $server->getName()) return $process;
        }
        return null;
    }

    public function getIdManager(): IdManager {
        return $this->idManager;
    }

    public function getPortManager(): PortManager {
        return $this->portManager;
    }

    public function getServer(string $name): ?Server {
        foreach ($this->servers as $server) {
            if ($server->getName() == $name) return $server;
        }
        return null;
    }

    public function getServersOfTemplate(Template $template): array {
        $servers = [];
        foreach ($this->getServers() as $server) if ($server->getTemplate()->getName() == $template->getName()) $servers[] = $server;
        return $servers;
    }

    public function getServers(): array
    {
        return $this->servers;
    }

    public function getProcesses(): array {
        return $this->processes;
    }

    public static function getInstance(): ServerManager {
        return self::$instance;
    }
}