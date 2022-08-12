<?php

namespace CloudBridge;

use CloudBridge\commands\CloudCommand;
use CloudBridge\event\PacketReceiveEvent;
use CloudBridge\listener\PacketListener;
use CloudBridge\network\CloudBridgeSocket;
use CloudBridge\network\protocol\packet\LoginRequestPacket;
use CloudBridge\network\protocol\packet\Packet;
use CloudBridge\network\protocol\packet\TestPacket;
use CloudBridge\network\utils\Address;
use pocketmine\block\BaseSign;
use pocketmine\block\utils\SignText;
use pocketmine\block\VanillaBlocks;
use pocketmine\math\Vector3;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\snooze\SleeperNotifier;
use pocketmine\utils\Config;
use pocketmine\utils\Process;
use pocketmine\utils\Utils;

class CloudBridge extends PluginBase {

    public static function getPrefix(): string {
        return "§8[§b§lCloud§r§8] §r§7";
    }

    private static self $instance;
    private CloudBridgeSocket $socket;

    protected function onEnable(): void {
        self::$instance = $this;
        $buffer = new \Threaded();
        $socketNotifier = new SleeperNotifier();
        $this->socket = new CloudBridgeSocket(new Address("127.0.0.1", $this->getCloudPort()), $socketNotifier, $buffer);

        $this->loadPermissions();

        $this->getServer()->getPluginManager()->registerEvents(new PacketListener(), $this);
        $this->getServer()->getCommandMap()->register("cloud", new CloudCommand("cloud", "Cloud Command", "/cloud", []));

        $this->getServer()->getTickSleeper()->addNotifier($socketNotifier, function () use($buffer): void {
            while (($packet = $buffer->shift()) !== null) (new PacketReceiveEvent($packet))->call();
        });

        $this->socket->connect();
        $this->socket->start(PTHREADS_INHERIT_INI | PTHREADS_INHERIT_CONSTANTS);

        $this->socket->sendPacket(LoginRequestPacket::create($this->getServerName()));

        file_put_contents($this->getServer()->getDataPath() . "pid.txt", getmypid());
    }

    protected function onDisable(): void {
        $this->socket->close();
        @Process::kill(Process::pid(), true);
    }

    public function sendPacket(Packet $packet) {
        $this->socket->sendPacket($packet);
    }

    public function getServerName(): string {
        return $this->getServerProperties()->get("motd");
    }

    public function getTemplate(): string {
        return $this->getServerProperties()->get("template");
    }

    public function getCloudPort(): int {
        return $this->getServerProperties()->get("cloud-port");
    }

    public function getCloudPath(): string {
        return $this->getServerProperties()->get("cloud-path");
    }

    public function getSocket(): CloudBridgeSocket {
        return $this->socket;
    }

    public function getServerProperties(): Config {
        return new Config(Server::getInstance()->getDataPath() . "server.properties", 0);
    }

    private function loadPermissions() {
        $operator = PermissionManager::getInstance()->getPermission(DefaultPermissions::ROOT_OPERATOR);
        if ($operator !== null) {
            DefaultPermissions::registerPermission(new Permission("cloud.command"), [$operator]);
            DefaultPermissions::registerPermission(new Permission("notify.receive"), [$operator]);
        }
    }

    public static function getInstance(): CloudBridge {
        return self::$instance;
    }
}