<?php

namespace Cloud\network\protocol\packet;

class DispatchCommandPacket extends Packet {

    public string $server = "";
    public string $commandLine = "";

    public function encode(): void {
        parent::encode();
        $this->put($this->server);
        $this->put($this->commandLine);
    }

    public function decode(): void {
        parent::decode();
        $this->server = $this->get();
        $this->commandLine = $this->get();
    }

    public function getId(): int {
        return self::ID_DISPATCH_COMMAND;
    }

    public static function create(string $server, string $commandLine): self {
        $pk = new self;
        $pk->server = $server;
        $pk->commandLine = $commandLine;
        return $pk;
    }
}