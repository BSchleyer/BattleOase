<?php

namespace Cloud\network\protocol\packet;

class RegisterServerPacket extends Packet {

    public string $name = "";
    private int $port = 0;

    public function encode(): void {
        parent::encode();
        $this->put($this->name);
        $this->put($this->port);
    }

    public function decode(): void {
        parent::decode();
        $this->name = $this->get();
        $this->port = $this->get();
    }

    public function getId(): int {
        return self::ID_REGISTER_SERVER;
    }

    public static function create(string $name, int $port): self {
        $pk = new self;
        $pk->name = $name;
        $pk->port = $port;
        return $pk;
    }
}