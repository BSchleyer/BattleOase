<?php

namespace Cloud\network\protocol\packet;

use Cloud\network\protocol\ProtocolInfo;

abstract class Packet extends ProtocolInfo {

    private array $packetContent = [];

    public function encode(): void {
        $this->put($this->getId());
    }

    public function decode(): void {
        $this->get();
    }

    public function put($value) {
        $this->packetContent[] = $value;
    }

    public function get(): mixed {
        if (count($this->packetContent) > 0) {
            $get = $this->packetContent[0];
            unset($this->packetContent[0]);
            $this->packetContent = array_values($this->packetContent);
            return $get;
        }
        return null;
    }

    public function getPacketContent(): array {
        return $this->packetContent;
    }

    public function setPacketContent(array $packetContent): void {
        $this->packetContent = $packetContent;
    }

    public function getName(): string {
        return (new \ReflectionClass($this))->getShortName();
    }

    abstract public function getId(): int;
}