<?php

namespace Cloud\scheduler;

class TaskScheduler {

    private static self $instance;
	/** @var Task[] */
	protected array $tasks = [];
	protected int $tick = 0;
	
	public function __construct() {
		self::$instance = $this;
	}

	public function scheduleTask(Task $task) {
		$this->tasks[$task->id] = $task;
	}

	public function getTask(int $id): ?Task {
		return @$this->tasks[$id];
	}

	public function cancel(Task|int $task) {
		if ($task instanceof Task) $task = $task->id;
		if (isset($this->tasks[$task])) unset($this->tasks[$task]);
	}

    public function cancelAll() {
	    foreach ($this->tasks as $task) {
	        $this->cancel($task);
        }
    }

	public function getTasks(): array {
		return $this->tasks;
	}

	public function onUpdate() {
		$this->tick++;
		
		foreach ($this->tasks as $id => $task) {
			if ($task->isCancelled()) {
				unset($this->tasks[$id]);
				continue;
			}
			$task->executeUpdate($this->tick);
		}
	}

    public static function getInstance(): TaskScheduler {
        return self::$instance;
    }
}