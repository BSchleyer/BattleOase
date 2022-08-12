<?php

namespace Cloud\network\protocol\packet;

class UnregisterServerPacket extends Packet {

    public string $name = "";

    public function encode(): void {
        parent::encode();
        $this->put($this->name);
    }

    public function decode(): void {
        parent::decode();
        $this->name = $this->get();
    }

    public function getId(): int {
        return self::ID_UNREGISTER_SERVER;
    }

    public static function create(string $name): self {
        $pk = new self;
        $pk->name = $name;
        return $pk;
    }
}