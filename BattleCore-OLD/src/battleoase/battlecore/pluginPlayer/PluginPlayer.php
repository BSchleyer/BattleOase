<?php


namespace battleoase\battlecore\pluginPlayer;


use battleoase\battlecore\pluginPlayer\listener\FormPacketListener;
use battleoase\battlecore\pluginPlayer\listener\PlayerJoinListener;
use battleoase\battlecore\pluginPlayer\listener\PlayerQuitListener;
use battleoase\battlecore\utils\BPlugin;

class PluginPlayer extends BPlugin {


    public function __construct()
    {
        $this->getServer()->getPluginManager()->registerEvents(new PlayerJoinListener(), $this->getPlugin());
		$this->getServer()->getPluginManager()->registerEvents(new PlayerQuitListener(), $this->getPlugin());
		$this->getServer()->getPluginManager()->registerEvents(new FormPacketListener(), $this->getPlugin());

    }

}