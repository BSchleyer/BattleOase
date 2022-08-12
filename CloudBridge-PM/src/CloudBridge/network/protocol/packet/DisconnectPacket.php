<?php

namespace CloudBridge\network\protocol\packet;

class DisconnectPacket extends Packet {

    const SERVER_SHUTDOWN = 0;
    const CLOUD_SHUTDOWN = 1;

    public int $code = 0;

    public function encode(): void {
        parent::encode();
        $this->put($this->code);
    }

    public function decode(): void {
        parent::decode();
        $this->code = $this->get();
    }

    public function getId(): int {
        return self::ID_DISCONNECT;
    }

    public static function create(int $code): self {
        $pk = new self;
        $pk->code = $code;
        return $pk;
    }
}