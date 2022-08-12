<?php

namespace Cloud\network\utils;

class Address {

    private string $address;
    private int $port;

    public function __construct(string $address, int $port) {
        $this->address = $address;
        $this->port = $port;
    }

    public function getAddress(): string {
        return $this->address;
    }

    public function getPort(): int {
        return $this->port;
    }

    public function __toString(): string {
        return $this->address . ":" . $this->port;
    }

    public function equals(Address $address): bool {
        return $address->getAddress() == $this->getAddress() && $address->getPort() == $this->getPort();
    }
}