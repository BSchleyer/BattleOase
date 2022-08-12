<?php


namespace battleoase\bedwars\utils;

use battleoase\battlecore\groupSystem\GroupSystem;
use battleoase\bedwars\api\TeamAPI;
use battleoase\bedwars\BedWars;
use battleoase\bedwars\caches\TeamCache;
use battleoase\bedwars\classes\Team;
use battleoase\lobbycore\LobbyCore;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;

class PlayerScoreboard
{
	use ScoreTrait;

	public function scoreboard(Player $player)
	{
		if (BedWars::getInstance()->ingame == true) {

			$this->removeScoreboard($player, "ingame");
			if (BedWars ::getInstance()->saveDamager == false) {
				$this->createScoreboard($player, " §3•§b● §b§lBattleOase.NET §b●§3•", "ingame");
				$this->addLine($player, 0, "§l§1", "ingame");

				$this->addLine($player, 2, "§8● §7Map", "ingame");
				$this->addLine($player, 3, "§8•§e  " . BedWars ::getInstance()->getArena()->getName(), "ingame");
				$this->addLine($player, 4, "§l§1§5         ", "ingame");
				$this->addLine($player, 5, "§8● §7Gold", "ingame");
				$this->addLine($player, 6, "§8•§a  " . ((BedWars ::getInstance()->no <= BedWars::getInstance()->yes) ? "On" : "§cOff"), "ingame");

				$this->addLine($player, 7, "§l§3§4§9 §0§5         ", "ingame");
				$x = 8;

				//$player->sendPopup("§cKills: §e" . BedWars::getInstance()->kill[$player->getName()] . " §8| §cBeds: §e" . BedWars::getInstance()->bed[$player->getName()]);

				foreach (TeamAPI ::getTeams() as $teamName) {
					$team = TeamCache ::get($teamName);
					$max = $team->getMaxPlayer();
					$players = count($team->getPlayers());

					if ($players > 0) $color = "§e"; else $color = "§3";

					$this->addLine($player, $x, "§8". $team->getTeamIcon() . " " . $team->getDisplayName() . " §7({$color}{$players}§7) " . ($team->hasBed() ? " §a✔" : " §c✖"), "ingame");
					$x++;
				}
			}
		} else {
			$this->createScoreboard($player, " §3•§b● §b§lBattleOase.NET §b●§3•", "ingame");
			$this->addLine($player, 0, "§l§1", "ingame");
			$this->addLine($player, 1, "§c  Loading Score-Data...", "ingame");

			BedWars::getInstance()->getScheduler()->scheduleDelayedTask(new class($player) extends Task{
				public function __construct(public Player $player) {}

				public function onRun(): void
				{
					$bwPlayer = BedWars::getInstance()->getPlayerManager()->getPlayer($this->player->getName());
					$playerTeam = TeamAPI::getTeamColor($bwPlayer->getTeam()->getName()) . $bwPlayer->getTeam()->getName();

					BedWars::getInstance()->getPlayerScoreboard()->removeScoreboard($this->player, "ingame");
					BedWars::getInstance()->getPlayerScoreboard()->createScoreboard($this->player, " §3•§b● §b§lBattleOase.NET §b●§3•", "ingame");
					BedWars::getInstance()->getPlayerScoreboard()->addLine($this->player, 0, "§l§1", "ingame");

					BedWars::getInstance()->getPlayerScoreboard()->addLine($this->player, 1, "§8● §7Team", "ingame");
					BedWars::getInstance()->getPlayerScoreboard()->addLine($this->player, 2, "§8•§e  " . $playerTeam, "ingame");
					BedWars::getInstance()->getPlayerScoreboard()->addLine($this->player, 4, "§l§1§5               ", "ingame");
					BedWars::getInstance()->getPlayerScoreboard()->addLine($this->player, 5, "§8● §7Replay-ID", "ingame");
					BedWars::getInstance()->getPlayerScoreboard()->addLine($this->player, 6, "§8•§c  ✖", "ingame");
					BedWars::getInstance()->getPlayerScoreboard()->addLine($this->player, 7, "§l§3§4§9 §0§5          ", "ingame");
					BedWars::getInstance()->getPlayerScoreboard()->addLine($this->player, 8, "§7Waiting for Players..", "ingame");
				}
			}, 40);
		}
	}

}