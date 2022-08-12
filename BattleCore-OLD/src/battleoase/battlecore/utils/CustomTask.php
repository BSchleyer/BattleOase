<?php


namespace battleoase\battlecore\utils;


use battleoase\battlecore\BattleCore;
use pocketmine\scheduler\ClosureTask;

class CustomTask
{
	public function __construct(int $delay, \Closure $closure) {
		BattleCore::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask($closure), $delay);
	}
}