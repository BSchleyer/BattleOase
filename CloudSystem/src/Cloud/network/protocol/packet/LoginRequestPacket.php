<?php

namespace Cloud\network\protocol\packet;

class LoginRequestPacket extends Packet {

    public string $server = "";

    public function encode(): void {
        parent::encode();
        $this->put($this->server);
    }

    public function decode(): void {
        parent::decode();
        $this->server = $this->get();
    }

    public function getId(): int {
        return self::ID_LOGIN_REQUEST;
    }

    public static function create(string $server): self {
        $pk = new self;
        $pk->server = $server;
        return $pk;
    }
}