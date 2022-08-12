<?php


namespace battleoase\battlecore\coinSystem;


use battleoase\battlecore\coinSystem\commands\CoinsCommand;
use battleoase\battlecore\utils\BPlugin;
use pocketmine\Server;

class CoinSystem extends BPlugin
{
	public function __construct()
	{
		Server::getInstance()->getCommandMap()->register("coins", new CoinsCommand());
	}
}