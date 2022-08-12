<?php


namespace battleoase\lobbycore\task;


use battleoase\lobbycore\LobbyCore;
use pocketmine\scheduler\Task;

class UpdateScoreTask extends Task
{

	public function onRun(): void
	{
		LobbyCore::updateLobbyScoreboard();
	}
}