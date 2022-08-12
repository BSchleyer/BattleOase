<?php


namespace battleoase\battlecore\clanSystem\api;


class PlayerClan
{

    private string $rank;
	private string $clan;
	private int $id;

	public function __construct(int $id,string $clan,string $rank)
    {
        $this->id = $id;
        $this->clan = $clan;
        $this->rank = $rank;
    }

    /**
     * @return string
     */
    public function getClanName(): string
    {
        return $this->clan;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getRank(): string
    {
        return $this->rank;
    }

}