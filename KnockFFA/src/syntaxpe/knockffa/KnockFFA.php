<?php

namespace syntaxpe\knockffa;

use battleoase\battlecore\BattleCore;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\plugin\PluginBase;
use syntaxpe\knockffa\events\EntityDamageListener;
use syntaxpe\knockffa\events\PlayerJoinListener;
use syntaxpe\knockffa\player\PlayerManager;

class KnockFFA extends PluginBase{

	const PREFIX = "§l§cKnock§6FF§cA §f§r§8» §r§7";

	/** @var KnockFFA */
	private static KnockFFA $instance;
	/** @var PlayerManager */
	private PlayerManager $playerManager;

	public bool $saveDamager = false;
	public bool $ingame = true;

	public int $no = 0;
	public int $yes = 0;
	public static int $i = 0;

	public array $lastdamager = [];

	public string $currentArena = "world";

	public function onEnable(): void
	{
		self::$instance = $this;

		//mlgRush::getInstance()->getScoreboard()->setScoreType(new LobbyScoreboard(), $this->player, "data");

		$this->playerManager = new PlayerManager();

		if(!InvMenuHandler::isRegistered()){
			InvMenuHandler::register($this);
		}

		$this->getServer()->getPluginManager()->registerEvents(new EntityDamageListener(), $this);
		$this->getServer()->getPluginManager()->registerEvents(new PlayerJoinListener(), $this);

		BattleCore::getInstance()->statsSystem->createStatsTable("KnockFFA");
	}

	/**
	 * @return KnockFFA
	 */
	public static function getInstance(): KnockFFA
	{
		return self::$instance;
	}

	/**
	 * @return PlayerManager
	 */
	public function getPlayerManager(): PlayerManager
	{
		return $this->playerManager;
	}
}