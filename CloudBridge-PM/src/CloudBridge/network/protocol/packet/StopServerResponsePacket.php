<?php

namespace CloudBridge\network\protocol\packet;

class StopServerResponsePacket extends Packet {

    const SUCCESS = 0;
    const ERROR = 1;

    public string $player = "";
    public string $message = "";
    public int $code = 0;

    public function encode(): void {
        parent::encode();
        $this->put($this->player);
        $this->put($this->message);
        $this->put($this->code);
    }

    public function decode(): void {
        parent::decode();
        $this->player = $this->get();
        $this->message = $this->get();
        $this->code = $this->get();
    }

    public function getId(): int {
        return self::ID_STOP_SERVER_RESPONSE;
    }

    public static function create(string $player, string $message, int $code): self {
        $pk = new self;
        $pk->player = $player;
        $pk->message = $message;
        $pk->code = $code;
        return $pk;
    }
}