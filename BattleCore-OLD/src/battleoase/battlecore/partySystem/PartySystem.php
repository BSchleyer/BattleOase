<?php


namespace battleoase\battlecore\partySystem;

use battleoase\battlecore\BattleCore;
use battleoase\battlecore\partySystem\commands\PartyCommand;
use battleoase\battlecore\partySystem\listener\EventListener;
use battleoase\battlecore\partySystem\utils\Database;
use battleoase\battlecore\partySystem\utils\Utils;
use battleoase\battlecore\utils\BPlugin;
use pocketmine\Server;

class PartySystem extends BPlugin
{

	protected static Database $database;
	protected static Utils $utils;

	public function __construct()
	{
		Server::getInstance()->getLogger()->info("§eLoaded plugin.");

		self::$database = new Database();
		self::$utils = new Utils();

		Server::getInstance()->getPluginManager()->registerEvents(new EventListener(), BattleCore::getInstance());
		Server::getInstance()->getCommandMap()->registerAll("PARTY", [
			new PartyCommand(),
		]);
		Server::getInstance()->getLogger()->info("§aEnabled plugin.");
	}

	/**
	 * @return Database
	 */
	public static function getDatabase(): Database
	{
		return self::$database;
	}

	/**
	 * @return Utils
	 */
	public static function getUtils(): Utils
	{
		return self::$utils;
	}

}