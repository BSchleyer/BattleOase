<?php

namespace battleoase\battlecore\replaySystemPlayer\manager;

use battleoase\battlecore\replaySystemPlayer\EventListener;
use battleoase\battlecore\replaySystemPlayer\ReplaySystemPlayer;
use battleoase\battlecore\replaySystemPlayer\scheduler\PlayReplayTask;
use battleunity\core\utils\Math;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\world\World;

class Replay {

    /** @var array  */
    private $entities = [];
    /** @var array  */
    private $actions = [];
    /** @var string  */
    private string $replayId = "";
    /** @var int  */
    private $playType = self::REPLAY_NORMAL;
    /** @var Vector3|null  */
    private $spectatorSpawn = null;
    /** @var array  */
    public $spawnedEntitiesFakeId = [];
    /** @var array  */
    public $spawnedEntitiesRealId = [];
    /** @var int  */
    private $lastTick = 0;
    /** @var bool */
    private $running = false;
    /** @var int  */
    private $nextTick = 0;
    /** @var int  */
    public $currentTick = 0;

    /** @var int  */
    public const REPLAY_PAUSED = 2;
    /** @var int  */
    public const REPLAY_BACKWARDS = 1;
    /** @var int  */
    public const REPLAY_NORMAL = 0;
    private int $speed = 0;

    /**
     * @param string $replayId
     */

    public function playReplay(string $replayId): void {
        $level = Server::getInstance()->getLevelByName("replayworld");
        if(!is_null($level)) {
            Server::getInstance()->unloadLevel($level);
        }
        ReplaySystemPlayer::getInstance()->deleteDir("worlds/replayworld");

        ReplaySystemPlayer::getInstance()->copymap("/root/network/replaysystem/".$replayId, "worlds/replayworld");
        $data = file_get_contents("/root/network/replaysystem/" . $replayId . "/data.json");
        $data = gzuncompress($data);
        $data = unserialize($data);
        $this->entities = unserialize($data["Entities"]);
        $this->actions = unserialize($data["Actions"]);
        $this->lastTick = $data["Duration"];

        $this->replayId = $replayId;
        $this->spectatorSpawn = math::stringVectorToVector3($data["SpectatorSpawn"], ":");

        Server::getInstance()->loadLevel("replayworld");
        foreach (Server::getInstance()->getOnlinePlayers() as $player){
            $player->teleport(new Position(0, 100, 0, Server::getInstance()->getLevelByName("replayworld")));
            $player->teleport($this->spectatorSpawn);
        }
        $this->setRunning();
        ReplaySystemPlayer::getInstance()->getScheduler()->scheduleDelayedTask(new PlayReplayTask($this, 0),20 * 3);

    }


    public function loadContents(array $items) : array
    {
        $contents = [];
        foreach ($items as $item) {
            $slot =  $item["slot"];
            $item = $this->getItemByItemData($item);
            $contents[$slot] = $item;
        }
        return $contents;

    }

    public function getItemByItemData(array $data) {
        $count = $data["count"];
        if($count != str_replace("-", "", $count)) {
            $countArray = explode("-", $count);
            $count = rand(... $countArray);
        }
        $item  = Item::get($data["id"], $data["meta"], $count);
        if($item->getMaxStackSize() < $count) {
            $item->setCount($item->getMaxStackSize());
        }
        if(isset($data["name"])) {
            $item->setCustomName($data["name"]);
        }
        if(isset($data["enchantment"])) {
            foreach ($data["enchantment"] as $enachantment) {
                $enachantment_data = explode(":", $enachantment);
                $e = Enchantment::getEnchantment(($enachantment_data[0]));
                $item->addEnchantment(new EnchantmentInstance($e, $enachantment_data[1]));
            }
        }
        return $item;
    }

    /**
     * @return array
     */

    public function getEntities() : array {
        return $this->entities;
    }

    /**
     * @return Vector3
     */

    public function getSpectatorSpawn(): Vector3 {
        return $this->spectatorSpawn;
    }

    /**
     * @return array
     */

    public function getActions() : array {
        return $this->actions;
    }

    /**
     * @return int
     */

    public function getPlayType(): int {
        return $this->playType;
    }

    /**
     * @param int $type
     */

    public function setPlayType(int $type): void {
        $this->playType = $type;
    }

    /**
     * @return bool
     */

    public function isPaused(): bool {
        return $this->getPlayType() === self::REPLAY_PAUSED;
    }

    /**
     * @return World
     */

    public function getLevel() : World {
        return Server::getInstance()->getWorldManager()->getWorldByName("replayworld");
    }

    /**
     * @return int
     */

    public function getLastTick() : int {
        return $this->lastTick;
    }

    /**
     * @param bool $running
     */

    public function setRunning(bool $running = true){
        $this->running = $running;
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            $replay = ReplaySystemPlayer::getInstance()->getReplay();
            $inv = $player->getInventory();
            $inv->setContents(($replay->isPaused() ? $this->loadContents(EventListener::PLAY_REPLAY_ITEMS) : $this->loadContents(EventListener::PAUSE_REPLAY_ITEMS)));
        }
    }

    /**
     * @return bool
     */

    public function isRunning() : bool {
        return $this->running;
    }

    /**
     * @return int
     */

    public function getNextTick(): int {
        return $this->nextTick + $this->getSpeed();
    }

    /**
     * @param int $tick
     */

    public function setNextTick(int $tick): void {
        $this->nextTick = $tick;
    }

    public function getSpeed()
    {
        return $this->speed;
    }

    public function setSpeed(int $speed)
    {
        if(($speed) < 0) return;
        $this->speed = $speed;
    }

    public function addSpeed(int $speed)
    {
        if(($this->speed - $speed) == 0) return;
        $this->speed = $this->speed + $speed;
    }
}