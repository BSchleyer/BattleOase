<?php

namespace Cloud\network\protocol\packet;

class ListServersRequestPacket extends Packet {

    public string $player = "";

    public function encode(): void {
        parent::encode();
        $this->put($this->player);
    }

    public function decode(): void {
        parent::decode();
        $this->player = $this->get();
    }

    public function getId(): int {
        return self::ID_LIST_SERVERS_REQUEST;
    }

    public static function create(string $player): self {
        $pk = new self;
        $pk->player = $player;
        return $pk;
    }
}