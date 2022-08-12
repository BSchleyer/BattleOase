<?php

namespace SignSystem\objects;

use ceepkev77\lobbyapi\LobbyAPI;
use pocketmine\world\Position;
use pocketmine\world\World;
use SignSystem\SignSystem;

class GroupSign {

    const SEARCH = 0;
    const MAINTENANCE = 1;

    private string $groupName;
    private Position $position;
    private int $state;
    private NULL|String $founder;
    private int $currentFormatIndex = 0;

    public function __construct(string $groupName, Position $position, bool $maintenance) {
        $this->groupName = $groupName;
        $this->position = $position;
        $this->state = ($maintenance == true) ? self::MAINTENANCE : self::SEARCH;
        $this->founder = null;
    }

    public function updateFormat(): void {
        if($this->state == self::SEARCH) {

        } else {

        }
    }

    public function nextFormatIndex(): array {
        $format = ($this->getFounder() !== null ? SignSystem::getInstance()->getSignConfig()->getFormat("online") : SignSystem::getInstance()->getSignConfig()->getFormat("offline"));
        $formatIndex = [];
        if (isset($format[$this->currentFormatIndex])) {
            $formatIndex = (is_array($format[$this->currentFormatIndex]) ? $this->replaceFormatIndex($format[$this->currentFormatIndex]) : $this->replaceFormatIndex(explode("\n", $format[$this->currentFormatIndex])));
            $this->currentFormatIndex++;
        } else {
            $this->currentFormatIndex = 0;
            if (isset($format[$this->currentFormatIndex])) {
                $formatIndex = (is_array($format[$this->currentFormatIndex]) ? $this->replaceFormatIndex($format[$this->currentFormatIndex]) : $this->replaceFormatIndex(explode("\n", $format[$this->currentFormatIndex])));
                $this->currentFormatIndex++;
            }
        }
        return $formatIndex;
    }

    private function replaceFormatIndex(array $formatIndex): array {
        $newFormatIndex = [];
        foreach ($formatIndex as $str) {
            if (is_array($str)) continue;
            $newFormatIndex[] = str_replace(
                ["&", "%server_name%", "%online_count%", "%max_players%"],
                [
                    "§",
                    ($this->getFounder() !== null ? $this->getFounder() : $this->getGroupName()),
                    ($this->getFounder() !== null ? LobbyAPI::getGameServerProvider()->getGameServer($this->getFounder())->getPlayerCount() : 0),
                    ($this->getFounder() !== null ? LobbyAPI::getGameServerProvider()->getGameServer($this->getFounder())->getCloudGroup()->getMaxPlayer() : 0),
                ],
                $str
            );
        }
        return $newFormatIndex;
    }

    /**
     * @return int
     */
    public function getCurrentFormatIndex(): int {
        return $this->currentFormatIndex;
    }

    /**
     * @return string
     */
    public function getGroupName(): string {
        return $this->groupName;
    }

    /**
     * @return Position
     */
    public function getPosition(): Position {
        return $this->position;
    }

    /**
     * @return int
     */
    public function getState(): int {
        return $this->state;
    }

    public function isInMaintenance(): bool {
        return $this->getState() == self::MAINTENANCE;
    }

    /**
     * @return String|null
     */
    public function getFounder(): ?string {
        return $this->founder;
    }

    /**
     * @param String|null $founder
     */
    public function setFounder(?string $founder): void
    {
        $this->founder = $founder;
    }
}