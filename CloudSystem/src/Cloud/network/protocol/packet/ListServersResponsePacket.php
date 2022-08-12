<?php

namespace Cloud\network\protocol\packet;

class ListServersResponsePacket extends Packet {

    public string $player = "";
    public array $servers = [];

    public function encode(): void {
        parent::encode();
        $this->put($this->player);
        $this->put($this->servers);
    }

    public function decode(): void {
        parent::decode();
        $this->player = $this->get();
        $this->servers = $this->get();
    }

    public function getId(): int {
        return self::ID_LIST_SERVERS_RESPONSE;
    }

    public static function create(string $player, array $servers): self {
        $pk = new self;
        $pk->player = $player;
        $pk->servers = $servers;
        return $pk;
    }
}