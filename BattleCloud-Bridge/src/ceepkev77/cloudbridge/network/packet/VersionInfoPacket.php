<?php

namespace ceepkev77\cloudbridge\network\packet;

use ceepkev77\cloudbridge\CloudBridge;
use ceepkev77\cloudbridge\network\DataPacket;
use ceepkev77\cloudbridge\objects\VersionInfo;

class VersionInfoPacket extends DataPacket
{

    public string $name;
    public string $author;
    public string $version;
    public string $identifier;

    public function handle()
    {
        $this->name = $this->data["name"];
        $this->author = $this->data["author"];
        $this->version = $this->data["version"];
        $this->identifier = $this->data["identifier"];
        CloudBridge::$versionInfo = new VersionInfo($this->name, $this->author, $this->version, $this->identifier);
        parent::handle(); // TODO: Change the autogenerated stub
    }

}