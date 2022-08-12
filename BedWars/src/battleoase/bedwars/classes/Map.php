<?php


namespace battleoase\bedwars\classes;


class Map
{

    public string $name;
    public int $votes = 0;
    public bool $goldVote = false;

	public int $no = 0;
	public int $yes = 0;

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param int $votes
     */
    public function setVotes(int $votes): void
    {
        $this->votes = $votes;
    }

	/**
	 * @param bool $goldVote
	 */
	public function setGoldVote(bool $goldVote): void
	{
		$this->goldVote = $goldVote;
	}

	/**
	 * @return bool
	 */
	public function getGoldVote(): bool
	{
		return $this->goldVote;
	}

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getVotes(): int
    {
        return $this->votes;
    }

	/**
	 * @return int
	 */
	public function getYes(): int
	{
		return $this -> yes;
	}

	/**
	 * @param int $yes
	 */
	public function setYes(int $yes): void
	{
		$this -> yes = $yes;
	}

	/**
	 * @return int
	 */
	public function getNo(): int
	{
		return $this -> no;
	}

	/**
	 * @param int $no
	 */
	public function setNo(int $no): void
	{
		$this -> no = $no;
	}


}