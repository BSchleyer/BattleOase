<?php

namespace CloudBridge\network\protocol\packet;

class PlayerInfoRequestPacket extends Packet {

    public string $target = "";
    public string $player = "";

    public function encode(): void {
        parent::encode();
        $this->put($this->target);
        $this->put($this->player);
    }

    public function decode(): void {
        parent::decode();
        $this->target = $this->get();
        $this->player = $this->get();
    }

    public function getId(): int
    {
        return self::ID_PLAYER_INFO_REQUEST;
    }

    public static function create(string $target, string $player): self {
        $pk = new self;
        $pk->target = $target;
        $pk->player = $player;
        return $pk;
    }
}