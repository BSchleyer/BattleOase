<?php


namespace syntaxpe\knockffa\utils\scoreboard\types;


use battleoase\battlecore\BattleCore;
use pocketmine\player\Player;
use syntaxpe\knockffa\utils\scoreboard\ScoreboardTrait;
use syntaxpe\knockffa\utils\scoreboard\ScoreType;

class PlayingScoreboard extends ScoreType
{
	use ScoreboardTrait;

	public function onScoreboard(Player $player, string $data = "")
	{
		$name = $player->getName();

		$kills = BattleCore::getInstance()->statsSystem->getKill($name, "KnockFFA");
		$deaths = BattleCore::getInstance()->statsSystem->getDeath($name, "KnockFFA_");
		$elo = BattleCore::getInstance()->statsSystem->getElo($name, "KnockFFA");

		$this->removeScoreboard($player, "ffa");
		$this->createScoreboard($player, "§b•§3● Battle§bOase §8| §cKnock§6FF§cA §b●§3•", "SB");
		$this->addLine($player, 0, "§l§1", "SB");
		$this->addLine($player, 1, "§7Kills:", "SB");
		$this->addLine($player, 2, "§a$kills", "SB");
		$this->addLine($player, 3, "§l§1§5         ", "SB");
		$this->addLine($player, 4, "§7Deaths:", "SB");
		$this->addLine($player, 5, "§c$deaths", "SB");
		$this->addLine($player, 6, "§l§1§7         ", "SB");
		$this->addLine($player, 7, "§7Elo:", "SB");
		$this->addLine($player, 8, "§e$elo", "SB");
		$this->addLine($player, 9, "§l§3§4§9 §0§5         ", "SB");
		$this->addLine($player, 10, "§3Battle§bOase", "SB");
	}
}