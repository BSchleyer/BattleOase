<?php


namespace battleoase\battlecore\clanSystem\api;


class Clan
{

    private string $clan_name;
    private string $clan_tag;
	private int $elo;
    private int $state;
	private string $owner;
	private string $created_at;
	private string $color;
	private string $custom_info;

	private int $loses_cw;
	private int $wins_cw;

	public function __construct(string $clan_name, string $clan_tag, int $elo, int $state, string $owner, string $created_at, string $color, string $custom_info, int $loses_cw, int $wins_cw)
    {
        $this->clan_name = $clan_name;
        $this->clan_tag = $clan_tag;
        $this->elo = $elo;
        $this->state = $state;
		$this->owner = $owner;
		$this->created_at = $created_at;
        $this->color = $color;
        $this->custom_info = $custom_info;
        $this->loses_cw = $loses_cw;
        $this->wins_cw = $wins_cw;
    }

	/**
	 * @return string
	 */
	public function getClanName(): string {
		return $this->clan_name;
	}

	/**
	 * @param string $clan_name
	 */
	public function setClanName(string $clan_name): void {
		$this->clan_name = $clan_name;
	}

	/**
	 * @return string
	 */
	public function getClanTag(): string {
		return $this->clan_tag;
	}

	/**
	 * @param string $clan_tag
	 */
	public function setClanTag(string $clan_tag): void {
		$this->clan_tag = $clan_tag;
	}

	/**
	 * @return int
	 */
	public function getElo(): int {
		return $this->elo;
	}

	/**
	 * @param int $elo
	 */
	public function setElo(int $elo): void {
		$this->elo = $elo;
	}

	/**
	 * @return int
	 */
	public function getState(): int {
		return $this->state;
	}

	/**
	 * @param int $state
	 */
	public function setState(int $state): void {
		$this->state = $state;
	}

	/**
	 * @return string
	 */
	public function getOwner(): string {
		return $this->owner;
	}

	/**
	 * @param string $owner
	 */
	public function setOwner(string $owner): void {
		$this->owner = $owner;
	}

	/**
	 * @return string
	 */
	public function getCreatedAt(): string {
		return $this->created_at;
	}

	/**
	 * @param string $created_at
	 */
	public function setCreatedAt(string $created_at): void {
		$this->created_at = $created_at;
	}

	/**
	 * @return string
	 */
	public function getColor(): string {
		return $this->color;
	}

	/**
	 * @param string $color
	 */
	public function setColor(string $color): void {
		$this->color = $color;
	}

	/**
	 * @return string
	 */
	public function getCustomInfo(): string {
		return $this->custom_info;
	}

	/**
	 * @param string $custom_info
	 */
	public function setCustomInfo(string $custom_info): void {
		$this->custom_info = $custom_info;
	}

	/**
	 * @return int
	 */
	public function getLosesCw(): int {
		return $this->loses_cw;
	}

	/**
	 * @param int $loses_cw
	 */
	public function setLosesCw(int $loses_cw): void {
		$this->loses_cw = $loses_cw;
	}

	/**
	 * @return int
	 */
	public function getWinsCw(): int {
		return $this->wins_cw;
	}

	/**
	 * @param int $wins_cw
	 */
	public function setWinsCw(int $wins_cw): void {
		$this->wins_cw = $wins_cw;
	}
}