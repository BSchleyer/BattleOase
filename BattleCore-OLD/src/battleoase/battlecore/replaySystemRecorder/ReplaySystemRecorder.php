<?php

namespace battleoase\battlecore\replaySystemRecorder;

use battleoase\battlecore\replaySystemRecorder\command\ReplayCommand;
use battleoase\battlecore\replaySystemRecorder\manager\Replay;
use battleoase\battlecore\replaySystemRecorder\tasks\CustomEventTask;
use battleoase\battlecore\utils\BPlugin;

class ReplaySystemRecorder extends BPlugin
{

    private ?Replay $replay;

    public function __construct()
    {
        $this->replay = new Replay();

		$this->getPlugin()->getScheduler()->scheduleRepeatingTask(new CustomEventTask(), 1);
		new EventListener();
		$this->getServer()->getCommandMap()->register("RS", new ReplayCommand());
    }

	/**
	 * @return Replay|null
	 */
	public function getReplay(): ?Replay
	{
		return $this->replay;
	}

	public function copymap($src, $dst) {
		$dir = opendir($src);
		@mkdir($dst);
		while (false !== ($file = readdir($dir))) {
			if (($file != '.') && ($file != '..')) {
				if (is_dir($src . '/' . $file)) {
					$this->copymap($src . '/' . $file, $dst . '/' . $file);
				} else {
					copy($src . '/' . $file, $dst . '/' . $file);
				}
			}
		}
		closedir($dir);
	}

}