<?php


namespace syntaxpe\knockffa\utils\scoreboard;

use pocketmine\player\Player;

class ScoreType
{
	public int $id = -1;

	/**
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId(int $id): void
	{
		$this->id = $id;
	}

	public function onScoreboard(Player $player, string $data) {}
}