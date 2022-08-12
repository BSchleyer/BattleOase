<?php

namespace CloudBridge\network\protocol\packet;

class StartServerRequestPacket extends Packet {

    public string $player = "";
    public string $template = "";
    public int $count = 1;

    public function encode(): void {
        parent::encode();
        $this->put($this->player);
        $this->put($this->template);
        $this->put($this->count);
    }

    public function decode(): void {
        parent::decode();
        $this->player = $this->get();
        $this->template = $this->get();
        $this->count = $this->get();
    }

    public function getId(): int {
        return self::ID_START_SERVER_REQUEST;
    }

    public static function create(string $player, string $template, int $count = 1): self {
        $pk = new self;
        $pk->player = $player;
        $pk->template = $template;
        $pk->count = $count;
        return $pk;
    }
}