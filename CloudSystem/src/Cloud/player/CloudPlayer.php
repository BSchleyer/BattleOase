<?php

namespace Cloud\player;

use Cloud\network\CloudSocket;
use Cloud\network\protocol\packet\PlayerKickPacket;
use Cloud\network\protocol\packet\TextPacket;
use Cloud\network\utils\Address;

class CloudPlayer {

    private string $name;
    private Address $address;
    private string $uuid;
    private string $xuid;
    private string $currentServer;
    private string $currentProxy = "";

    public function __construct(string $name, Address $address, string $uuid, string $xuid, string $currentServer = "", string $currentProxy = "") {
        $this->name = $name;
        $this->address = $address;
        $this->uuid = $uuid;
        $this->xuid = $xuid;
        $this->currentServer = $currentServer;
        $this->currentProxy = $currentProxy;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getAddress(): Address {
        return $this->address;
    }

    public function getCurrentServer(): string {
        return $this->currentServer;
    }

    public function getUuid(): string {
        return $this->uuid;
    }

    public function getXuid(): string {
        return $this->xuid;
    }

    public function getCurrentProxy(): string {
        return $this->currentProxy;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function setAddress(Address $address): void {
        $this->address = $address;
    }

    public function setCurrentServer(string $currentServer): void {
        $this->currentServer = $currentServer;
    }

    public function setUuid(string $uuid): void {
        $this->uuid = $uuid;
    }

    public function setXuid(string $xuid): void {
        $this->xuid = $xuid;
    }

    public function setCurrentProxy(string $currentProxy): void {
        $this->currentProxy = $currentProxy;
    }

    public function sendMessage(string $message) {
        CloudSocket::getInstance()->broadcastPacket(
            TextPacket::create($this->getName(), $message, TextPacket::TYPE_MESSAGE)
        );
    }

    public function sendTitle(string $title) {
        CloudSocket::getInstance()->broadcastPacket(
            TextPacket::create($this->getName(), $title, TextPacket::TYPE_TITLE)
        );
    }

    public function sendPopup(string $popup) {
        CloudSocket::getInstance()->broadcastPacket(
            TextPacket::create($this->getName(), $popup, TextPacket::TYPE_POPUP)
        );
    }

    public function sendTip(string $tip) {
        CloudSocket::getInstance()->broadcastPacket(
            TextPacket::create($this->getName(), $tip, TextPacket::TYPE_TIP)
        );
    }

    public function sendActionbarMessage(string $message) {
        CloudSocket::getInstance()->broadcastPacket(
            TextPacket::create($this->getName(), $message, TextPacket::TYPE_ACTIONBAR)
        );
    }

    public function kick(string $reason = "No reason given.") {
        CloudSocket::getInstance()->broadcastPacket(
            PlayerKickPacket::create($this->getName(), $reason)
        );
    }
}