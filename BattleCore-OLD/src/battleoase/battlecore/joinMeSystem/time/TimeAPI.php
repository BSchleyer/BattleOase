<?php


namespace battleoase\battlecore\joinMeSystem\time;


class TimeAPI
{
	public static function convert(int $time): TimeFormat{
		$c = $time - time();
		if($c >= 3600){
			$hours = floor($c/3600);
			$c = $c - $hours*3600;
		} else {
			$hours = 0;
		}
		if($c >= 60){
			$minutes = floor($c/60);
			$c = $c - $minutes*60;
		} else {
			$minutes = 0;
		}
		if($c >= 1) {
			$seconds =  floor($c/1);
		} else {
			$seconds = 0;
		}
		return new TimeFormat($hours, $minutes, $seconds);
	}

	public static function getTimeFromArray(array $times) : TimeFormat
	{
		$hours = 0; $minutes = 0; $seconds = 0;
		foreach ($times as $time) {
			if ($time != ($result = str_replace("h", "", $time))) {
				$hours = $result;
			} elseif ($time != ($result = str_replace("m", "", $time))) {
				$minutes = $result;
			} elseif ($time != ($result = str_replace("s", "", $time))) {
				$seconds = $result;
			}
		}
		return new TimeFormat(intval($hours), intval($minutes), intval($seconds));
	}

	public static function isTimeValid(int $time)
	{
		if ($time != 0) {
			$c = $time - time();
			if ($c <= 0) {
				return false;
			}
		}

		return true;
	}
}