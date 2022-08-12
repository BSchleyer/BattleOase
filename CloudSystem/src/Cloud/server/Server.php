<?php

namespace Cloud\server;

use Cloud\Cloud;
use Cloud\player\CloudPlayer;
use Cloud\server\status\ServerStatus;
use Cloud\template\Template;
use Cloud\utils\Config;

class Server {

    private string $name;
    private int $id;
    private int $port;
    private Template $template;
    /** @var CloudPlayer[] */
    private array $players = [];
    private int|float $startTime;
    private Config $properties;
    private int $serverStatus = ServerStatus::STATUS_STARTING;
    private float|int $lastConnectionCheckTime;
    private bool $gotConnectionResponse = false;

    public function __construct(string $name, int $id, int $port, Template $template, float|int $startTime) {
        $this->name = $name;
        $this->id = $id;
        $this->port = $port;
        $this->template = $template;
        $this->startTime = $startTime;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getPort(): int {
        return $this->port;
    }

    public function getTemplate(): Template {
        return $this->template;
    }

    /** @return CloudPlayer[] */
    public function getPlayers(): array {
        return $this->players;
    }

    public function getPlayersCount(): int {
        return count($this->getPlayers());
    }

    public function getStartTime(): float|int {
        return $this->startTime;
    }

    public function getProperties(): Config {
        return $this->properties;
    }

    public function getServerStatus(): int {
        return $this->serverStatus;
    }

    public function setServerStatus(int $serverStatus): void {
        $this->serverStatus = $serverStatus;
    }

    public function getLastConnectionCheckTime(): float|int {
        return $this->lastConnectionCheckTime;
    }

    public function setLastConnectionCheckTime(float|int $lastConnectionCheckTime): void {
        $this->lastConnectionCheckTime = $lastConnectionCheckTime;
    }

    public function isGotConnectionResponse(): bool {
        return $this->gotConnectionResponse;
    }

    public function setGotConnectionResponse(bool $gotConnectionResponse): void {
        $this->gotConnectionResponse = $gotConnectionResponse;
    }

    public function addPlayer(CloudPlayer $player) {
        if (!isset($this->players[$player->getName()])) $this->players[$player->getName()] = $player;
    }

    public function removePlayer(CloudPlayer $player) {
        $players = $this->players;
        if (array_key_exists($player->getName(), $players)) {
            unset($players[$player->getName()]);
            $this->players = $players;
            array_push($this->players);
        }
    }

    public function getPath(): string {
        return CLOUD_PATH . "temp/servers/" . $this->getName() . "/";
    }

    public function createProperties() {
        if ($this->template->getType() == Template::TYPE_SERVER) {
            $this->properties = new Config($this->getPath() . "server.properties", 0);
        } else {
            $this->properties = new Config($this->getPath() . "config.yml", 2);
        }

        $cfg = $this->properties;
        if ($this->template->getType() == Template::TYPE_SERVER) {
            foreach ($cfg->getAll() as $key => $value) {
                $cfg->set($key, $value);
            }
            $cfg->set("max-players", $this->template->getMaxPlayers());
            $cfg->set("enable-ipv6", "off");
            $cfg->set("language", "eng");
            $cfg->set("xbox-auth", "off");
            $cfg->set("server-port", $this->port);
            $cfg->set("motd", $this->name);
            $cfg->set("server-name", $this->name);
            $cfg->set("template", $this->template->getName());
            $cfg->set("cloud-port", Cloud::getInstance()->getSocketPort());
            $cfg->set("cloud-path", CLOUD_PATH);
            $cfg->save();
        } else {
            foreach ($cfg->getAll() as $key => $value) {
                $cfg->set($key, $value);
            }
            $cfg->set("listener", ["motd" => str_replace("{server}", $this->getName(), Cloud::getInstance()->getProxyMotd()), "priorities" => [], "host" => "0.0.0.0:" . $this->getPort(), "max_players" => $this->getTemplate()->getMaxPlayers()]);
            $cfg->set("use_login_extras", false);
            $cfg->set("cloud-path", CLOUD_PATH);
            $cfg->set("cloud-port", Cloud::getInstance()->getSocketPort());
            $cfg->set("server-name", $this->name);
            $cfg->set("template", $this->template->getName());
            $cfg->save();
        }
    }
}
