<?php


namespace battleoase\lobbycore\commands;


use battleoase\battlecore\BattleCore;
use battleoase\lobbycore\utils\Utils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\CommandException;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\particle\FloatingTextParticle;

class SpawnHologram extends Command
{
	public function __construct()
	{
		parent::__construct("spawnholo", "SpawnHolo Command");
		$this->setPermission("admin");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if ($sender instanceof Player){
			if (isset($args[0]) && isset($args[1])) {
				$utils = new Utils();
				$utils->setSpawnOfWelcomeInfo($sender);
				$utils->spawnWelcomeInfo();

				/*foreach (Server::getInstance()->getOnlinePlayers() as $p){
					$playerStatsWins = BattleCore::getInstance()->statsSystem->getWins($p, "MLGRush");
					$playerStatsLoses = BattleCore::getInstance()->statsSystem->getLoses($p, "MLGRush");

					$particle = new FloatingTextParticle("\n §eWins: §r§f".$playerStatsWins."\n\n §eLoses: §r§f".$playerStatsLoses."",
						"§7× §eYour All-Time §b§lMLG§r§f§lRush §r§f§eStats");
					$sender->getWorld()->addParticle($sender->getPosition()->asVector3(), $particle, [$p]);
				}*/

			}
		}
	}
}