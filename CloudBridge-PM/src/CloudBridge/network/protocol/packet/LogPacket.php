<?php

namespace CloudBridge\network\protocol\packet;

class LogPacket extends Packet {

    public string $message = "";

    public function encode(): void {
        parent::encode();
        $this->put($this->message);
    }

    public function decode(): void {
        parent::decode();
        $this->message = $this->get();
    }

    public function getId(): int {
        return self::ID_LOG;
    }

    public static function create(string $message): self {
        $pk = new self;
        $pk->message = $message;
        return $pk;
    }
}