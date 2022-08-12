<?php

namespace Cloud\server\task;

use Cloud\api\NotifyAPI;
use Cloud\network\CloudSocket;
use Cloud\scheduler\Task;
use Cloud\scheduler\TaskScheduler;
use Cloud\server\id\IdManager;
use Cloud\server\port\PortManager;
use Cloud\server\Server;
use Cloud\server\ServerManager;
use Cloud\server\status\ServerStatus;
use Cloud\template\Template;
use Cloud\utils\CloudLogger;
use Cloud\utils\Utils;

class CheckServerStartupTask extends Task {

    private Server $server;
    private int $count = 10;

    public function __construct(Server $server) {
        $this->server = $server;
        parent::__construct(0, true, 20);
    }

    public function onRun(int $tick): void {
        if ($this->count == 0) {
            $this->server->setServerStatus(ServerStatus::STATUS_STOPPED);
            CloudLogger::getInstance()->info("The server §e" . $this->server->getName() . " §7could §cnot §7be started!");
            NotifyAPI::getInstance()->sendNotify("The server §e" . $this->server->getName() . " §7cloud §cnot §7be started!");
            ServerManager::getInstance()->closeProcess($this->server);
            ServerManager::getInstance()->removeServer($this->server);
            IdManager::getInstance()->removeId($this->server->getTemplate(), $this->server->getId());
            PortManager::getInstance()->removePort($this->server->getPort());
            CloudSocket::getInstance()->unverify($this->server->getName());
            ServerManager::getInstance()->removePid($this->server);
            Utils::deleteDir($this->server->getPath());
            $this->count = 10;
            TaskScheduler::getInstance()->cancel($this);
        } else {
            if (CloudSocket::getInstance()->isVerifiedServer($this->server)) {
                $this->server->setServerStatus(ServerStatus::STATUS_STARTED);
                CloudLogger::getInstance()->info("The server §e" . $this->server->getName() . " §rwas §astarted §ron port §e" . $this->server->getPort() . "§r!");
                NotifyAPI::getInstance()->sendNotify("The server §e" . $this->server->getName() . " §7was §astarted §7on port §e" . $this->server->getPort() . "§7!");
                TaskScheduler::getInstance()->scheduleTask(new ConnectionTask($this->server));
                if ($this->server->getTemplate()->getType() == Template::TYPE_PROXY) ServerManager::getInstance()->addAllServersToProxy($this->server);
                $this->count = 10;
                TaskScheduler::getInstance()->cancel($this);
            }
        }
        $this->count--;
    }
}