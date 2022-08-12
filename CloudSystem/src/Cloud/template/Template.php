<?php

namespace Cloud\template;

class Template {

    const TYPE_SERVER = 0;
    const TYPE_PROXY = 1;

    private string $name;
    private int $minServers;
    private int $maxServers;
    private int $maxPlayers;
    private bool $autoStart;
    private int $type;

    public function __construct(string $name, int $minServers, int $maxServers, int $maxPlayers, bool $autoStart, int $type = self::TYPE_SERVER) {
        $this->name = $name;
        $this->minServers = $minServers;
        $this->maxServers = $maxServers;
        $this->maxPlayers = $maxPlayers;
        $this->autoStart = $autoStart;
        $this->type = $type;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getMinServers(): int {
        return $this->minServers;
    }

    public function getMaxServers(): int {
        return $this->maxServers;
    }

    public function getMaxPlayers(): int {
        return $this->maxPlayers;
    }

    public function isAutoStart(): bool {
        return $this->autoStart;
    }

    public function getType(): int {
        return $this->type;
    }

    public function getPath(): string {
        return CLOUD_PATH . "templates/" . $this->getName() . "/";
    }
}