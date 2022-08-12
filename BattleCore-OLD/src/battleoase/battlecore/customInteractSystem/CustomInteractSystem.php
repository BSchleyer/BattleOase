<?php


namespace battleoase\battlecore\customInteractSystem;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\utils\BPlugin;
use pocketmine\Server;

class CustomInteractSystem extends BPlugin
{
	public array $delay = [];

	public function __construct()
	{
		Server::getInstance()->getPluginManager()->registerEvents(new CustomInteractListener(), BattleCore::getInstance());
	}

}