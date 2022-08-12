<?php


namespace syntaxpe\knockffa\utils\scoreboard;


use pocketmine\player\Player;

class Scoreboard
{
	private ScoreType $scoreType;

	public function setScoreType(ScoreType $scoreType, Player $player, string $data)
	{
		$this->scoreType = $scoreType;
		$this->scoreType->onScoreboard($player, $data);
	}

	/**
	 * @return ScoreType
	 */
	public function getScoreType(): ScoreType
	{
		return $this->scoreType;
	}


}