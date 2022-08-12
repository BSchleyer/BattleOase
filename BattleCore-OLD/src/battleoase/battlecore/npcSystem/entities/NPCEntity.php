<?php

namespace battleoase\battlecore\npcSystem\entities;

use Closure;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\EmotePacket;
use pocketmine\player\Player;
use pocketmine\world\ChunkLoader;
use pocketmine\world\format\Chunk;

class NPCEntity extends Human implements ChunkLoader {

    /** @var array */
    private $emotes = [];
    /** @var int */
    private $emoteCooldown = 0;
    /** @var string */
    private $lastEmote = "";

    /** @var bool */
    private $lookAtPlayer = false;

    /** @var Closure|null */
    private $interactClosure = null;
    /** @var Closure|null */
    private $attackClosure = null;

    /**
     * NPCEntity constructor.
     * @param Location $location
     * @param Skin $skin
     */
    public function __construct(Location $location, Skin $skin)
    {
        $location->getWorld()->loadChunk($location->x >> 4, $location->z >> 4);
        $this->skin = $skin;
        $this->setNameTagVisible();
        $this->setNameTagAlwaysVisible();
       // $this->sendSkin();
        parent::__construct($location, $skin);
    }

    /**
     * @param array $emotes
     */
    public function setEmotes(array $emotes): void
    {
        $this->emotes = $emotes;
    }

    /**
     * @param string $emote
     * @return $this
     */
    public function addEmote(string $emote): self
    {
        $this->emotes[] = $emote;
        return $this;
    }

    /**
     * @return array
     */
    public function getEmotes(): array
    {
        return $this->emotes;
    }

    /**
     * @return string|null
     */
    public function getRandomEmote(): ?string
    {
        if ($this->emotes === []) return null;
        return $this->emotes[array_rand($this->emotes)];
    }

    /**
     * @param Closure|null $closure
     */
    public function setInteractClosure(?Closure $closure): void {
        $this->interactClosure = $closure;
    }

    /**
     * @param Closure|null $closure
     */
    public function setAttackClosure(?Closure $closure): void {
        $this->attackClosure = $closure;
    }

    /**
     * @param bool $lookAtPlayer
     */
    public function setLookAtPlayer(bool $lookAtPlayer): void {
        $this->lookAtPlayer = $lookAtPlayer;
    }

    /**
     * @param float $yaw
     * @param float $pitch
     */
    public function setRotation(float $yaw, float $pitch): void{
        parent::setRotation($yaw, $pitch);
        $this->setForceMovementUpdate();
    }

    /**
     * @param int $currentTick
     * @return bool
     */
    public function onUpdate(int $currentTick): bool{
        if($this->lookAtPlayer) {
            $target = $this->getWorld()->getNearestEntity($this->getDirectionVector(), 40, Player::class);
            if($target !== null) {
                $this->lookAt($target->add(0, 1));
                $this->setForceMovementUpdate();
            }
        }

        if($this->getEmotes() !== [] && --$this->emoteCooldown <= 0) {
            $this->emoteCooldown = mt_rand(100, 300);
            $emote = $this->getRandomEmote();
            while($this->lastEmote === $emote) $emote = $this->getRandomEmote();
            $packet = EmotePacket::create($this->getId(), $emote, EmotePacket::FLAG_SERVER);
            $this->getWorld()->broadcastPacketToViewers($this->getPosition(), $packet);

            if(count($this->getEmotes()) > 1) $this->lastEmote = $emote;
        }
        return parent::onUpdate($currentTick);
    }

    /**
     * @param EntityDamageEvent $source
     */
    public function attack(EntityDamageEvent $source): void{
        if(!$source instanceof EntityDamageByEntityEvent) return;
        $player = $source->getDamager();
        if(!$player instanceof Player) return;
        if($this->attackClosure === null) return;
        ($this->attackClosure)($player);
    }

    /**
     * @param Player $player
     * @param Vector3 $clickPos
     * @return bool
     */
    public function onInteract(Player $player, Vector3 $clickPos): bool
    {
        if($this->interactClosure === null) return false;
        ($this->interactClosure)($player);
        return true;
    }

    /**
     * @param int $tickDiff
     * @return bool
     */
    public function entityBaseTick(int $tickDiff = 1): bool{
        return true;
    }

    /**
     * @return int
     */
    public function getLoaderId(): int{
        return spl_object_id($this);
    }

    /**
     * @return bool
     */
    public function isLoaderActive(): bool{
        return !$this->isClosed();
    }

    public function onChunkChanged(Chunk $chunk){}
    public function onChunkLoaded(Chunk $chunk){}
    public function onChunkUnloaded(Chunk $chunk){}
    public function onChunkPopulated(Chunk $chunk){}
    public function onBlockChanged(Vector3 $block){}
}