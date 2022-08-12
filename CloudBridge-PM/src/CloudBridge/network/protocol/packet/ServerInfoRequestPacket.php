<?php

namespace CloudBridge\network\protocol\packet;

class ServerInfoRequestPacket extends Packet {

    public string $server = "";
    public string $player = "";

    public function encode(): void {
        parent::encode();
        $this->put($this->server);
        $this->put($this->player);
    }

    public function decode(): void {
        parent::decode();
        $this->server = $this->get();
        $this->player = $this->get();
    }

    public function getId(): int {
        return self::ID_SERVER_INFO_REQUEST;
    }

    public static function create(string $server, string $player): self {
        $pk = new self;
        $pk->server = $server;
        $pk->player = $player;
        return $pk;
    }
}