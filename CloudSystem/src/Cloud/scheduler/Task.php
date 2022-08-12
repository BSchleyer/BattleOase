<?php

namespace Cloud\scheduler;

abstract class Task {

	public int $id;
	private int $delay;
	private bool $repeating;
	private int $lastRun = 0;
	private bool $cancelled = false;
	private int $interval;

	public function __construct(int $delay = 0, bool $repeating = false, int $interval = 1) {
		do {
			$this->id = mt_rand(PHP_INT_MIN, PHP_INT_MAX);
		} while (TaskScheduler::getInstance()->getTask($this->id) instanceof Task);
		
		$this->delay = $delay;
		$this->repeating = $repeating;
		$this->interval = $interval;
	}

	public function cancel() {
		if ($this->isCancelled()) return;
		TaskScheduler::getInstance()->cancel($this);
	}

	public function isCancelled(): bool {
		return $this->cancelled;
	}

	public function executeUpdate(int $tick) {
		if ($this->delay > 0) {
			if (--$this->delay === 0) goto RUN;
			return;
		}
		
		if ($tick >= ($this->lastRun + $this->interval)) {
			RUN:
			$this->lastRun = $tick;
			$this->onRun($tick);
			if (!$this->repeating) $this->cancel();
		}
	}

	abstract public function onRun(int $tick): void;
}