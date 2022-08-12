<?php

namespace battleoase\lobbycore\commands;


use battleoase\lobbycore\forms\LobbyGamesForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class LobbyGamesCommand extends Command
{
	public function __construct()
	{
		parent::__construct("lobbygames", "LobbyGames Command", "/lg", ["lg"]);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if ($sender instanceof Player){
			$lobybGames = new LobbyGamesForm($sender);
			$lobybGames->open();
		}
	}
}