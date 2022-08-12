<?php


namespace battleoase\battlecore\chatLogSystem;


use battleoase\battlecore\chatLogSystem\log\Logger;
use battleoase\battlecore\utils\BPlugin;

class ChatLogSystem extends BPlugin {

	public function __construct() {
		$this->getServer()->getPluginManager()->registerEvents(new Logger(), $this->getPlugin());
	}

}