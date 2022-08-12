<?php

namespace CloudBridge\network\protocol\packet;

class PlayerInfoResponsePacket extends Packet {

    const SUCCESS = 0;
    const ERROR = 1;

    public string $name = "";
    public string $address = "";
    public int $port = 0;
    public string $uuid = "";
    public string $xuid = "";
    public string $currentServer = "";
    public string $currentProxy = "";
    public string $player = "";
    public int $code = 0;

    public function encode(): void {
        parent::encode();
        $this->put($this->name);
        $this->put($this->address);
        $this->put($this->port);
        $this->put($this->uuid);
        $this->put($this->xuid);
        $this->put($this->currentServer);
        $this->put($this->currentProxy);
        $this->put($this->player);
        $this->put($this->code);
    }

    public function decode(): void {
        parent::decode();
        $this->name = $this->get();
        $this->address = $this->get();
        $this->port = $this->get();
        $this->uuid = $this->get();
        $this->xuid = $this->get();
        $this->currentServer = $this->get();
        $this->currentProxy = $this->get();
        $this->player = $this->get();
        $this->code = $this->get();
    }

    public function getId(): int {
        return self::ID_PLAYER_INFO_RESPONSE;
    }

    public static function create(string $name, string $address, int $port, string $uuid, string $xuid, string $currentServer, string $currentProxy, string $player, int $code): self {
        $pk = new self;
        $pk->name = $name;
        $pk->address = $address;
        $pk->port = $port;
        $pk->uuid = $uuid;
        $pk->xuid = $xuid;
        $pk->currentServer = $currentServer;
        $pk->currentProxy = $currentProxy;
        $pk->player = $player;
        $pk->code = $code;
        return $pk;
    }
}