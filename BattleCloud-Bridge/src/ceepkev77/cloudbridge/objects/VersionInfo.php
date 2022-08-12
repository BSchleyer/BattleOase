<?php

namespace ceepkev77\cloudbridge\objects;

use pocketmine\Server;

class VersionInfo
{


    public function __construct(public String $name, public String $author, public String $version, public String $identifier)
    {
        if($this->identifier === "@Beta") {
            Server::getInstance()->getLogger()->warning("THE BATTLECLOUD IS RUNNING ON AN UNSTABLE VERSION (" . $this->version . $this->identifier . ")");
        }
    }

}