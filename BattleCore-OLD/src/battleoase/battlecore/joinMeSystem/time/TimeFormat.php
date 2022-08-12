<?php


namespace battleoase\battlecore\joinMeSystem\time;


class TimeFormat
{
	public function __construct(private int $hours, private int $minutes, private int $seconds) {}

	/**
	 * @return int
	 */
	public function getHours(): int
	{
		return $this->hours;
	}

	/**
	 * @return int
	 */
	public function getMinutes(): int
	{
		return $this->minutes;
	}

	/**
	 * @return int
	 */
	public function getSeconds(): int
	{
		return $this->seconds;
	}

	/**
	 * @return int
	 */

	public function getTime(): int
	{
		return $this->getHours() * 3600 + $this->getMinutes() * 60 + $this->getSeconds();
	}

	/**
	 * @return int
	 */

	public function getAddTime(): int
	{
		if ($this->getTime() == 0) {
			return 0;
		}
		return $time = intval($this->getTime() + time());
	}

	/**
	 * @return string
	 */

	public function asString(): string
	{
		if ($this->getTime() == 0) {
			return "Never (Permanent)";
		}
		return ($this->getHours() != 0 ? strval($this->getHours())." Hour(s), " : "") . ($this->getMinutes() != 0 ? strval($this->getMinutes())." Minute(s) " : "") . ($this->getSeconds() != 0 ? strval($this->getSeconds())." Second(s)" : "") ;
	}
}