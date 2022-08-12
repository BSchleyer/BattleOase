<?php

namespace CloudBridge\network\protocol\packet;

class PlayerKickPacket extends Packet {

    public string $player = "";
    public string $reason = "No reason given.";

    public function encode(): void {
        parent::encode();
        $this->put($this->player);
        $this->put($this->reason);
    }

    public function decode(): void {
        parent::decode();
        $this->player = $this->get();
        $this->reason = $this->get();
    }

    public function getId(): int {
        return self::ID_PLAYER_KICK;
    }

    public static function create(string $player, string $reason = "No reason given."): self {
        $pk = new self;
        $pk->player = $player;
        $pk->reason = $reason;
        return $pk;
    }
}