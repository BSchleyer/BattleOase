<?php

namespace CloudBridge\network\protocol\packet;

class LoginResponsePacket extends Packet {

    const SUCCESS = 0;
    const DENIED = 1;

    public int $responseCode = 0;

    public function encode(): void {
        parent::encode();
        $this->put($this->responseCode);
    }

    public function decode(): void {
        parent::decode();
        $this->responseCode = $this->get();
    }

    public function getId(): int {
        return self::ID_LOGIN_RESPONSE;
    }

    public static function create(int $responseCode): self {
        $pk = new self;
        $pk->responseCode = $responseCode;
        return $pk;
    }
}