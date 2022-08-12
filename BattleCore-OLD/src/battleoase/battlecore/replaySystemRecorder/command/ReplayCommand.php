<?php


namespace battleoase\battlecore\replaySystemRecorder\command;



use battleoase\battlecore\BattleCore;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class ReplayCommand extends Command
{

	public function __construct()
	{
		parent::__construct("replay", "Replay Admin", false, []);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if($sender instanceof Player) {
			if($sender->hasPermission("admin")) {
				if(BattleCore::getInstance()->replaySystemRecorder->getReplay()->isRunning()) {
					BattleCore::getInstance()->replaySystemRecorder->getReplay()->stopReplay();
					$sender->sendMessage(BattleCore::getPrefix() . "stop replay...");
				} else {
					BattleCore::getInstance()->replaySystemRecorder->getReplay()->startReplay($sender->getWorld(), $sender->getPosition());
					$sender->sendMessage(BattleCore::getPrefix() . "start replay...");
				}
			}
		}
	}
}