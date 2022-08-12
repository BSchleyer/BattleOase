<?php

namespace Cloud\server\port;

class PortManager {

    private static self $instance;
    private array $ports = [];

    public function __construct() {
        self::$instance = $this;
    }

    public function getFreePort(): int {
        for($i = 30000; $i < 40000; $i++) {
            if (in_array($i, $this->ports)) {
                continue;
            } else {
                return $i;
            }
        }
        return 0;
    }

    public function getFreeProxyPort(): int {
        for ($i = 19132; $i < 29132; $i++) {
            if (in_array($i, $this->ports)) {
                continue;
            } else {
                return $i;
            }
        }
        return 0;
    }

    public function addPort(int $port) {
        if (!in_array($port, $this->ports)) {
            array_push($this->ports, $port);
        }
    }

    public function removePort(int $port) {
        if (in_array($port, $this->ports)) {
            unset($this->ports[array_search($port, $this->ports)]);
        }
    }

    public function getPorts(): array {
        return $this->ports;
    }

    public static function getInstance(): PortManager {
        return self::$instance;
    }
}