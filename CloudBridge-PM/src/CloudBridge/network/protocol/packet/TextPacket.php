<?php

namespace CloudBridge\network\protocol\packet;

class TextPacket extends Packet {

    const TYPE_MESSAGE = 0;
    const TYPE_TITLE = 1;
    const TYPE_POPUP = 2;
    const TYPE_TIP = 3;
    const TYPE_ACTIONBAR = 4;

    public string $player = "";
    public string $message = "";
    public int $type = 0;

    public function encode(): void {
        parent::encode();
        $this->put($this->player);
        $this->put($this->message);
        $this->put($this->type);
    }

    public function decode(): void {
        parent::decode();
        $this->player = $this->get();
        $this->message = $this->get();
        $this->type = $this->get();
    }

    public function getId(): int {
        return self::ID_TEXT;
    }

    public static function create(string $player, string $message, int $type): self {
        $pk = new self;
        $pk->player = $player;
        $pk->message = $message;
        $pk->type = $type;
        return $pk;
    }
}