<?php


namespace battleoase\lobbycore\jumpAndRun;


use battleoase\lobbycore\jumpAndRun\commands\JumpLeaveCommand;
use battleoase\lobbycore\jumpAndRun\events\PlayerMoveListener;
use battleoase\lobbycore\LobbyCore;

class JumpAndRun
{
	public array $jump;
	public array $checkpoint;

	public string $prefix = "§e§lJumpAndRun§r§f §r§f§8× §7";

	public function __construct() {
		LobbyCore::getInstance()->getServer()->getPluginManager()->registerEvents(new PlayerMoveListener(), LobbyCore::getInstance());
		LobbyCore::getInstance()->getServer()->getCommandMap()->register(LobbyCore::getInstance()->getName(), new JumpLeaveCommand(new PlayerMoveListener()));

	}

	public function getPrefix() {
		return $this->prefix;
	}
}