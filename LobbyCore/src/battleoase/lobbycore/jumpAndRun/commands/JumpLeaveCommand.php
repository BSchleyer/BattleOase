<?php


namespace battleoase\lobbycore\jumpAndRun\commands;


use battleoase\battlecore\BattleCore;
use battleoase\lobbycore\jumpAndRun\events\PlayerMoveListener;
use battleoase\lobbycore\LobbyCore;
use battleoase\lobbycore\player\PlayerManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class JumpLeaveCommand extends Command
{
	private $plugin;

	public function __construct(PlayerMoveListener $plugin) {
		parent::__construct("jumpleave", "Leave the JumpAndRun ", "/jumpleave");
		$this->plugin = $plugin;
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if(!LobbyCore::getInstance()->getJumpAndRun()->jump[$sender->getName()] == false) {
			PlayerManager::getPlayer($sender)->giveItems();

			unset(LobbyCore::getInstance()->getJumpAndRun()->checkpoint[$sender->getName()]);
			LobbyCore::getInstance()->getJumpAndRun()->jump[$sender->getName()] = false;

			$sender->sendMessage(LobbyCore::getInstance()->getJumpAndRun()->getPrefix() . BattleCore::getInstance()->getLanguageSystem()->translate($sender, "jumpAndRun.message.lose"));
			foreach (Server::getInstance()->getOnlinePlayers() as $p) {
				$sender->showPlayer($p);
			}

			if ($sender instanceof Player) $sender->teleport(Server::getInstance()->getWorldManager()->getDefaultWorld()->getSafeSpawn());

		} else {
			$sender->sendMessage(LobbyCore::getInstance()->getJumpAndRun()->getPrefix() . BattleCore::getInstance()->getLanguageSystem()->translate($sender, "jumpAndRun.message.noingame"));
		}
	}
}