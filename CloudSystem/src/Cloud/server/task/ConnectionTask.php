<?php

namespace Cloud\server\task;

use Cloud\api\NotifyAPI;
use Cloud\network\CloudSocket;
use Cloud\network\protocol\packet\ConnectionPacket;
use Cloud\scheduler\Task;
use Cloud\scheduler\TaskScheduler;
use Cloud\server\id\IdManager;
use Cloud\server\port\PortManager;
use Cloud\server\Server;
use Cloud\server\ServerManager;
use Cloud\server\status\ServerStatus;
use Cloud\utils\CloudLogger;
use Cloud\utils\Utils;

class ConnectionTask extends Task {

    private Server $server;
    private bool $firstTime = true;

    public function __construct(Server $server) {
        $this->server = $server;
        parent::__construct(0, true, 20);
    }

    public function onRun(int $tick): void {
        if ($this->firstTime == true) {
            $this->firstTime = false;
            $this->server->setLastConnectionCheckTime(microtime(true));
            $client = CloudSocket::getInstance()->getClient($this->server->getName());
            if ($client !== null) CloudSocket::getInstance()->sendPacket(ConnectionPacket::create($this->server->getName()), $client);
        } else {
            if ($this->server->isGotConnectionResponse()) {
                $this->server->setGotConnectionResponse(false);
                $this->server->setLastConnectionCheckTime(microtime(true));
                $client = CloudSocket::getInstance()->getClient($this->server->getName());
                if ($client !== null) CloudSocket::getInstance()->sendPacket(ConnectionPacket::create($this->server->getName()), $client);
            } else {
                if (!ServerManager::getInstance()->isRunning($this->server)) { //server has stopped or crashed
                    $this->server->setServerStatus(ServerStatus::STATUS_STOPPED);

                    ServerManager::getInstance()->closeProcess($this->server);
                    ServerManager::getInstance()->removeServer($this->server);
                    IdManager::getInstance()->removeId($this->server->getTemplate(), $this->server->getId());
                    PortManager::getInstance()->removePort($this->server->getPort());
                    CloudSocket::getInstance()->unverify($this->server->getName());

                    $crashDumpFolderPath = $this->server->getPath() . "crashdumps/";
                    $time1 = date("D_M_j-H.i.s-T_Y", (microtime(true)-1));
                    $time2 = date("D_M_j-H.i.s-T_Y", (microtime(true)-2));
                    $time3 = date("D_M_j-H.i.s-T_Y", (microtime(true)-3));
                    $time4 = date("D_M_j-H.i.s-T_Y", (microtime(true)-4));
                    $time5 = date("D_M_j-H.i.s-T_Y", (microtime(true)+1));
                    $time6 = date("D_M_j-H.i.s-T_Y", (microtime(true)+2));

                    if (file_exists($this->server->getPath() . "crashdumps/")) Utils::copyDir($this->server->getPath() . "crashdumps/", $this->server->getTemplate()->getPath() . "crashdumps/");

                    if (file_exists($crashDumpFolderPath . $time1 . ".log") || file_exists($crashDumpFolderPath . $time2 . ".log") || file_exists($crashDumpFolderPath . $time3 . ".log") || file_exists($crashDumpFolderPath . $time4 . ".log") || file_exists($crashDumpFolderPath . $time5 . ".log") || file_exists($crashDumpFolderPath . $time6 . ".log")) {
                        CloudLogger::getInstance()->info("The server §e" . $this->server->getName() . " §rwas §4crashed§r!");
                        NotifyAPI::getInstance()->sendNotify("The server §e" . $this->server->getName() . " §7was §4crashed§7!");
                    } else {
                        CloudLogger::getInstance()->info("The server §e" . $this->server->getName() . " §rwas §cstopped§r!");
                        NotifyAPI::getInstance()->sendNotify("The server §e" . $this->server->getName() . " §7was §cstopped§7!");
                    }
                    Utils::deleteDir($this->server->getPath());
                    ServerManager::getInstance()->removePid($this->server);
                    TaskScheduler::getInstance()->cancel($this);
                }
            }
        }
    }
}