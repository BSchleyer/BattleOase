<?php

namespace battleoase\battlecore\replaySystemRecorder\events;

use pocketmine\event\Event;

class ReplaySaveEvent extends Event {

    /** @var string  */
    private string $roundType = "";
    /** @var string  */
    private string $serverType = "";
    /** @var string */
    private string $replayId;

    /**
     * ReplaySaveEvent constructor.
     * @param string $replayId
     */

    public function __construct(string $replayId) {
        $this->replayId = $replayId;
    }

    /**
     * @return string
     */

    public function getReplayId(): string {
        return $this->replayId;
    }

    /**
     * @return string
     */

    public function getRoundType(): string {
        return $this->roundType;
    }

    /**
     * @param string $type
     */

    public function setRoundType(string $type): void {
        $this->roundType = $type;
    }

    /**
     * @return string
     */

    public function getServerType(): string {
        return $this->serverType;
    }

    /**
     * @param string $serverType
     */

    public function setServerType(string $serverType): void {
        $this->serverType = $serverType;
    }
}