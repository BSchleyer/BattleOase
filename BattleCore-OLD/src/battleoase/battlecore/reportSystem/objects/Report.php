<?php


namespace battleoase\battlecore\reportSystem\objects;


use pocketmine\nbt\tag\StringTag;

class Report {

	private string $reported_player;
	private string $reported_by;
	private string $reason;
	private string $createdAt;
	private mixed $extraData;

	public function __construct(string $reported_player, string $reported_by, string $reason, string $createdAt, mixed $extraData) {
		$this->reported_player = $reported_player;
		$this->reported_by = $reported_by;
		$this->reason = $reason;
		$this->createdAt = $createdAt;
		$this->extraData = $extraData;
	}

	/**
	 * @return string
	 */
	public function getReportedPlayer(): string {
		return $this->reported_player;
	}

	/**
	 * @param string $reported_player
	 */
	public function setReportedPlayer(string $reported_player): void {
		$this->reported_player = $reported_player;
	}

	/**
	 * @return string
	 */
	public function getReportedBy(): string {
		return $this->reported_by;
	}

	/**
	 * @param string $reported_by
	 */
	public function setReportedBy(string $reported_by): void {
		$this->reported_by = $reported_by;
	}

	/**
	 * @return string
	 */
	public function getReason(): string {
		return $this->reason;
	}

	/**
	 * @param string $reason
	 */
	public function setReason(string $reason): void {
		$this->reason = $reason;
	}

	/**
	 * @return string
	 */
	public function getCreatedAt(): string {
		return $this->createdAt;
	}

	/**
	 * @param string $createdAt
	 */
	public function setCreatedAt(string $createdAt): void {
		$this->createdAt = $createdAt;
	}

	/**
	 * @return mixed
	 */
	public function getExtraData(): mixed {
		return $this->extraData;
	}

	/**
	 * @param mixed $extraData
	 */
	public function setExtraData(mixed $extraData): void {
		$this->extraData = $extraData;
	}

}