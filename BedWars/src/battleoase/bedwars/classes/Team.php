<?php


namespace battleoase\bedwars\classes;


use battleoase\bedwars\api\TeamAPI;
use pocketmine\player\Player;

class Team
{

    public string $name;
    public int $maxPlayer = 0;
    public bool $bed = true;
    public array $players = [];

    /**
     * @param int $maxPlayer
     */
    public function setMaxPlayer(int $maxPlayer): void
    {
        $this->maxPlayer = $maxPlayer;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param array $players
     */
    public function setPlayers(array $players): void
    {
        $this->players = $players;
    }

    /**
     * @param Player $player
     */
    public function addPlayer(Player $player): void
    {
        $this->players[] = $player->getName();
    }

    public function removePlayer(Player $player)
    {
		if (($key = array_search($player->getName(), $this->players)) !== false) {
			unset($this->players[$key]);
		}
    }

    /**
     * @param bool $bed
     */
    public function setBed(bool $bed): void
    {
        $this->bed = $bed;
    }

    /**
     * @return int
     */
    public function getMaxPlayer(): int
    {
        return $this->maxPlayer;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getPlayers(): array
    {
        return $this->players;
    }

    /**
     * @return bool
     */
    public function hasBed(): bool
    {
        return $this->bed;
    }

	public function getDisplayName()
	{
		return TeamAPI::getTeamColor($this->getName()) . $this->getName();
    }

    public function getTeamIcon(){
    	return TeamAPI::getTeamIcon($this->getName());
	}

}