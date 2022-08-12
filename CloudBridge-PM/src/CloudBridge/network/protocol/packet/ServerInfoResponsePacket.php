<?php

namespace CloudBridge\network\protocol\packet;

class ServerInfoResponsePacket extends Packet {

    const SUCCESS = 0;
    const ERROR = 1;

    public string $name = "";
    public int $id = 0;
    public string $template = "";
    public int $port = 0;
    public array $players = [];
    public int $maxPlayers = 0;
    public int $serverStatus = -1;
    public string $player = "";
    public int $code = 0;

    public function encode(): void{
        parent::encode();
        $this->put($this->name);
        $this->put($this->id);
        $this->put($this->template);
        $this->put($this->port);
        $this->put($this->players);
        $this->put($this->maxPlayers);
        $this->put($this->serverStatus);
        $this->put($this->player);
        $this->put($this->code);
    }

    public function decode(): void {
        parent::decode();
        $this->name = $this->get();
        $this->id = $this->get();
        $this->template = $this->get();
        $this->port = $this->get();
        $this->players = $this->get();
        $this->maxPlayers = $this->get();
        $this->serverStatus = $this->get();
        $this->player = $this->get();
        $this->code = $this->get();
    }

    public function getId(): int {
        return self::ID_SERVER_INFO_RESPONSE;
    }

    public static function create(string $name, int $id, string $template, int $port, array $players, int $maxPlayers, int $serverStatus, string $player, int $code): self {
        $pk = new self;
        $pk->name = $name;
        $pk->id = $id;
        $pk->template = $template;
        $pk->port = $port;
        $pk->players = $players;
        $pk->maxPlayers = $maxPlayers;
        $pk->serverStatus = $serverStatus;
        $pk->player = $player;
        $pk->code = $code;
        return $pk;
    }
}