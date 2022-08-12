<?php

namespace Cloud\network\protocol\packet;

class ProxyPlayerJoinPacket extends Packet {

    public string $name = "";
    public string $uuid = "";
    public string $xuid = "";
    public string $address = "";
    public int $port = 0;
    public string $currentProxy = "";

    public function encode(): void {
        parent::encode();
        $this->put($this->name);
        $this->put($this->uuid);
        $this->put($this->xuid);
        $this->put($this->address);
        $this->put($this->port);
        $this->put($this->currentProxy);
    }

    public function decode(): void {
        parent::decode();
        $this->name = $this->get();
        $this->uuid = $this->get();
        $this->xuid = $this->get();
        $this->address = $this->get();
        $this->port = $this->get();
        $this->currentProxy = $this->get();
    }

    public function getId(): int {
        return self::ID_PROXY_PLAYER_JOIN;
    }

    public static function create(string $name, string $uuid, string $xuid, string $address, int $port, string $currentProxy): self {
        $pk = new self;
        $pk->name = $name;
        $pk->uuid = $uuid;
        $pk->xuid = $xuid;
        $pk->address = $address;
        $pk->port = $port;
        $pk->currentProxy = $currentProxy;
        return $pk;
    }
}