<?php

namespace syntaxpe\knockffa\utils\scoreboard\types;

use battleoase\battlecore\BattleCore;
use pocketmine\player\Player;
use pocketmine\Server;
use syntaxpe\knockffa\utils\scoreboard\ScoreboardTrait;
use syntaxpe\knockffa\utils\scoreboard\ScoreType;

class VotingScoreboard extends ScoreType
{

	use ScoreboardTrait;

	public int $id = 0;

	/**
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
	}

	public function onScoreboard(Player $player, string $data = "")
	{
		$this->removeScoreboard($player, "SB");

		$this->removeScoreboard($player, "ffa");
		$this->createScoreboard($player, "§b•§3● Battle§bOase §8| §cKnock§6FF§cA §b●§3•", "SB");
		$this->addLine($player, 0, "§l§1", "SB");
		$this->addLine($player, 1, "§7Voting phase...", "SB");
		$this->addLine($player, 2, "§a", "SB");
		$this->addLine($player, 3, "§l§3§4§9 §0§5         ", "SB");
		$this->addLine($player, 4, "§3Battle§bOase", "SB");
	}


}