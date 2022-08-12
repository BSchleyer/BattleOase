<?php


namespace battleoase\battlecore\privateServerSystem;


use battleoase\battlecore\privateServerSystem\command\PrivateServerCommand;
use battleoase\battlecore\utils\BPlugin;

class PrivateServerSystem extends BPlugin
{

    public function __construct()
    {
        $this->getServer()->getCommandMap()->register("ps", new PrivateServerCommand());
    }

}