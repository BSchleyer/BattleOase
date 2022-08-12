<?php

namespace Cloud\scheduler;

class ClosureTask extends Task {
	
	public function __construct(protected $callable, int $delay = 0, bool $repeating = false, int $interval = 1) {
		parent::__construct($delay, $repeating, $interval);
	}
	
	public function onRun(int $tick): void {
		($this->callable)();
	}
}