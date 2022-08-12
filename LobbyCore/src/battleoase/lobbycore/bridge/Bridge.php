<?php


namespace battleoase\lobbycore\bridge;


use battleoase\lobbycore\bridge\events\PlayerMoveListener;
use battleoase\lobbycore\LobbyCore;

class Bridge
{
	public $bridge;
	public array $blocks = [];

	public string $prefix = "§9Bridge §r§f§8× §7";

	public function __construct()
	{
		LobbyCore::getInstance()->getServer()->getPluginManager()->registerEvents(new PlayerMoveListener($this), LobbyCore::getInstance());
	}
}