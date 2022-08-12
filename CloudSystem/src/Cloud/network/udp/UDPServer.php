<?php

namespace Cloud\network\udp;

use Cloud\network\utils\Address;

class UDPServer {

    private \Socket $socket;
    private bool $connected = false;
    private Address $address;

    public function bind(Address $address) {
        if ($this->connected) return;
        $this->address = $address;
        $this->socket = @socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        if(@socket_bind($this->socket, $address->getAddress(), $address->getPort()) === true) {
            $this->connected = true;
            @socket_set_option($this->socket, SOL_SOCKET, SO_SNDBUF, 1024 * 1024 * 8);
            @socket_set_option($this->socket, SOL_SOCKET, SO_RCVBUF, 1024 * 1024 * 8);
        }else {
            $error = socket_last_error($this->socket);
            if($error === SOCKET_EADDRINUSE) {
                throw new \Exception("Failed to bind socket: Something else is already running on $address", $error);
            } else {
                throw new \Exception("Failed to bind to $address: " . trim(socket_strerror($error)), $error);
            }
        }

        socket_set_nonblock($this->socket);
    }

    public function write(string $buffer, Address $dst): bool|int {
        if (!$this->isConnected()) return false;
        return @socket_sendto($this->socket, $buffer, strlen($buffer), 0, $dst->getAddress(), $dst->getPort());
    }

    public function read(?string &$buffer, ?string &$address, ?int &$port): bool|int {
        if (!$this->isConnected()) return false;
        return @socket_recvfrom($this->socket, $buffer, 65535, 0, $address, $port);
    }

    public function close() {
        if ($this->isConnected()) {
            $this->connected = false;
            @socket_close($this->socket);
        }
    }

    public function getAddress(): Address {
        return $this->address;
    }

    public function getSocket(): \Socket {
        return $this->socket;
    }

    public function isConnected(): bool {
        return $this->connected;
    }
}