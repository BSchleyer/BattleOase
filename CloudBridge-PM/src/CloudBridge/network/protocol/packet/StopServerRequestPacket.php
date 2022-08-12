<?php

namespace CloudBridge\network\protocol\packet;

class StopServerRequestPacket extends Packet {

    public string $player = "";
    public string $server = "";

    public function encode(): void {
        parent::encode();
        $this->put($this->player);
        $this->put($this->server);
    }

    public function decode(): void {
        parent::decode();
        $this->player = $this->get();
        $this->server = $this->get();
    }

    public function getId(): int {
        return self::ID_STOP_SERVER_REQUEST;
    }

    public static function create(string $player, string $server): self {
        $pk = new self;
        $pk->player = $player;
        $pk->server = $server;
        return $pk;
    }
}