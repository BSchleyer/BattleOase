<?php

namespace battleoase\battlecore\replaySystemRecorder\manager;

use battleoase\battlecore\BattleCore;
use battleoase\battlecore\replaySystemRecorder\events\ReplaySaveEvent;
use battleoase\battlecore\replaySystemRecorder\ReplaySystemRecorder;
use battleoase\battlecore\utils\Math;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\object\ItemEntity;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\world\World;

class Replay
{


    /** @var bool  */
    private bool $running = false;

    /** @var World|null */
    private ?World $world = null;

    /** @var array  */
    public array $actions = [];

    /** @var array  */
    public array $entities = [];

    /** @var array  */
    private array $detectedEntities = [];

    /** @var int  */
    private int $startTick;

    /** @var int  */
    private int $stopTick;

    /** @var string  */
    private string $replayId = "";

    /** @var Vector3 */
    private Vector3 $spectatorSpawn;
    private array $player = [];

    /**
     * @return bool
     */

    public function isRunning() : bool {
        return $this->running;
    }

    /**
     * @param bool $running
     */

    public function setRunning(bool $running = true) {
        $this->running = $running;
    }

    /**
     * @return World
     */

    public function getWorld() : World {
        return $this->world;
    }

    /**
     * @return Actions
     */

    public function addAction() {
        if(!$this->isRunning()){
            return null;
        }
        return new Actions($this);
    }

    /**
     * @return array
     */

    public function getActions() : array {
        return $this->actions;
    }

    /**
     * @param Entity $entity
     */

    public function addEntity(Entity $entity) {
        if($entity instanceof Player && $entity->isSpectator()) return;
        if(!in_array($entity->getId(), $this->detectedEntities)){
            $skin = null;
            $itemId = null;
            $itemMeta = null;
            $this->detectedEntities[] = $entity->getId();
            if($entity instanceof Human){
                $skin = serialize($entity->getSkin()->getSkinData());
                if($entity instanceof Player) {
                    $this->addAction()->itemHeldAction($entity);
                }
            }
            if($entity instanceof ItemEntity){
                $itemId = $entity->getItem()->getId();
                $itemMeta = $entity->getItem()->getMeta();
            }
            $tick = Server::getInstance()->getTick() - $this->startTick;
            if($tick === 0) {
                $tick = 2;
            }
            $this->entities[$tick][] = [
                "Id" => $entity->getId(),
                "NetworkID" => $entity::getNetworkTypeId(),
                "X" => $entity->getPosition()->x,
                "Y" => $entity->getPosition()->y,
                "Z" => $entity->getPosition()->z,
                "Yaw" => 0,
                "Pitch" => 0,
                "Nametag" => $entity->getNameTag(),
                "Skin" => $skin,
                "ItemId" => $itemId,
                "ItemMeta" => $itemMeta
            ];
        }
    }

    /**
     * @return array
     */

    public function getEntities() : array {
        return $this->entities;
    }

    /**
     * @param World $world
     * @return bool
     */

    public function isReplayWorld(World $world) : bool {
        if(is_null($this->getWorld())){
            return false;
        }
        if(!$this->isRunning()) {
            return false;
        }
        return $this->getWorld()->getDisplayName() === $world->getDisplayName();
    }

    /**
     * @return int
     */

    public function getStartTick() : int {
        return $this->startTick;
    }

    /**
     * @param World $world
     * @param Vector3 $vector3
     * @return string|null
     */

    public function startReplay(World $world, Vector3 $vector3): ?string {
        if($this->isRunning()) {
            return false;
        }
        $this->startTick = Server::getInstance()->getTick();
        $this->setRunning(true);
        $this->world = $world;
        $this->spectatorSpawn = $vector3;
        $this->replayId = BattleCore::getInstance()->generateRandomString(7);
        $this->running = true;
        $this->player = $this->onlinePlayerWithNameInArray();
        $path = "/home/cloud/data/replaysystem/";
        mkdir($path.$this->replayId);
        BattleCore::getInstance()->replaySystemRecorder->copymap("worlds/".$world->getFolderName(), $path.$this->replayId);
        return $this->replayId;
    }

	public function onlinePlayers() : array {
		$return = [];
		foreach (Server::getInstance()->getOnlinePlayers() as $player) {
			$return[] = $player->getName();
		}
		return $return;
	}

	public function onlinePlayerWithNameInArray() : array {
		$return = [];
		foreach (Server::getInstance()->getOnlinePlayers() as $player) {
			$return[] = $player->getName();
		}
		return $return;
	}

    public function stopReplay() {
        $this->stopTick = Server::getInstance()->getTick();
        $this->setRunning(false);
        $this->saveReplay();
    }

    /**
     * @return string
     */

    public function saveReplay(): string {
        $replayId = $this->replayId;
        $duration = $this->stopTick - $this->startTick;
        $actions = $this->actions;
        $entities = $this->getEntities();
        $spawn = Math::vector3ToString($this->spectatorSpawn);
        $ev = new ReplaySaveEvent($replayId);
        $ev->call();
        $data = [
            "Duration" => $duration,
            "SpectatorSpawn" => $spawn,
            "Actions" => serialize($actions),
            "Entities" => serialize($entities)
        ];
        $extraData = [
            "ServerType" => Server::getInstance()->getMotd(),
            "RoundType" => $ev->getRoundType(),
            "date" => date("Y-m-d H:i"),
            "PlayerName" => $this->player,
            "id" => $this->replayId,
        ];

        $data = serialize($data);
        $extraData = serialize($extraData);
        $path = "/home/cloud/data/replaysystem/";
        BattleCore::getInstance()->replaySystemRecorder->copymap("crashdumps", $path.$this->replayId);
        Server::getInstance()->getAsyncPool()->submitTask(
            new class($data, $extraData, $replayId) extends AsyncTask {

                /** @var string */
                private string $data;
                /** @var string */
                private string $extraData;
                /** @var string */
                private string $replayId;

                /**
                 *  constructor.
                 * @param string $data
                 * @param string $extraData
                 * @param string $replayId
                 */

                public function __construct(string $data, string $extraData, string $replayId) {
                    $this->data = $data;
                    $this->extraData = $extraData;
                    $this->replayId = $replayId;
                }


                public function onRun(): void {
                    $extraData = unserialize($this->extraData);
                    $replayId = $this->replayId;

                    $path = "/home/cloud/data/replaysystem/";
                    $extraData = json_encode($extraData);
                    file_put_contents($path.$replayId."/extraData.json", $extraData);
                    $data = gzcompress($this->data);
                    file_put_contents($path.$replayId."/data.json", $data);
                    $handle = fopen($path.$replayId."/data.json", "r+");
                    fwrite($handle, $data);
                    fclose($handle);
                }
            }
        );

        /*
        Server::getInstance()->getAsyncPool()->submitTask(
            new class($data, $extraData, $replayId, $actions, $entities) extends AsyncTask {

                private $extraData;
                private $data;
                private $replayId;

                public function __construct(array $data, array $extraData, string $replayId, array $actions, array $entities) {
                    $data["Actions"] = serialize($actions);
                    $data["Entities"] = serialize($entities);
                    $this->data = $data;
                    $this->extraData = $extraData;
                    $this->replayId = $replayId;
                }

                public function onRun() : void {
                    $extraData = $this->extraData;
                    $data = $this->data;
                    $replayId = $this->replayId;
                    $path = "/root/network/replaysystem/";

                    $extraData = json_encode($extraData);
                    file_put_contents($path.$replayId."/extraData.json", $extraData);
                    $data = serialize($data);
                    $data = gzcompress($data);
                    file_put_contents($path.$replayId."/data.json", $data);
                    $handle = fopen($path.$replayId."/data.json", "r+");
                    fwrite($handle, $data);
                    fclose($handle);
                }
            }
        );*/
        BattleCore::getInstance()->getLogger()->info("§aReplay wurde unter $replayId abgespeichert!");
        return $replayId;
    }

}