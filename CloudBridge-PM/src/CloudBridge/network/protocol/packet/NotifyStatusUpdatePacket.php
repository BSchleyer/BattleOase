<?php

namespace CloudBridge\network\protocol\packet;

class NotifyStatusUpdatePacket extends Packet {

    public string $player = "";
    public bool $v = true;

    public function encode(): void {
        parent::encode();
        $this->put($this->player);
        $this->put($this->v);
    }

    public function decode(): void {
        parent::decode();
        $this->player = $this->get();
        $this->v = $this->get();
    }

    public function getId(): int {
        return self::ID_NOTIFY_STATUS_UPDATE;
    }

    public static function create(string $player, bool $v): self {
        $pk = new self;
        $pk->player = $player;
        $pk->v = $v;
        return $pk;
    }
}