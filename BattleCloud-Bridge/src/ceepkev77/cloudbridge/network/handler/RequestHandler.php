<?php

namespace ceepkev77\cloudbridge\network\handler;

use ceepkev77\cloudbridge\CloudBridge;
use pocketmine\Server;
use Thread;

class RequestHandler extends Thread
{
    private $socket;
    /** @var bool */
    private $stop;
    /** @var array */
    public $queue;

    public function __construct()
    {
        $this->stop = false;
        $this->queue = [];

        try {
            $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            $this->socket = $socket;
            socket_connect($socket, "127.0.0.1", (int)CloudBridge::getInstance()->getCloudPort());
            CloudBridge::getInstance()->getLogger()->info("§bCloud §aConnection opened to 127.0.0.1:" . CloudBridge::getInstance()->getCloudPort());
            $this->start(PTHREADS_INHERIT_ALL);
        }catch (\Exception $e) {
            CloudBridge::getInstance()->getLogger()->critical("Connection to Cloud interrupted");
        }
    }


    public function getSocket()
    {
        return $this->socket;
    }

    public function run()
    {
        while(!$this->stop) {
            $request = null;
            try {
                $request = @socket_read($this->socket, 2048, PHP_NORMAL_READ);
            }catch (\Exception $e) {
                break;
            }

            if($request != null) {
                #PacketHandler::handleCloudPacket($request);
                $this->queue[$request] = $request;
            }
        }

        if($this->stop)
            socket_shutdown($this->socket);
    }

    /**
     * @param string $request
     */
    public function unsetRequest(string $request)
    {
        unset($this->queue[$request]);
    }

    public function write(string $data)
    {
        if($this->stop) return;

        socket_write($this->socket, $data.PHP_EOL);
    }

    /**
     * @return bool
     */
    public function isStop(): bool
    {
        return $this->stop;
    }

    public function stop()
    {
        $this->stop = true;
        CloudBridge::getInstance()->getLogger()->info("§bCloud §4Connection closed");
    }
}