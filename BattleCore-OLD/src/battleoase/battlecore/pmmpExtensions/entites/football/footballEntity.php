<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 13.05.2021
 * Time: 19:03
 */


namespace battleoase\battlecore\pmmpExtensions\entites\football;

use battleoase\battlecore\pmmpExtensions\math;
use pocketmine\block\Cobweb;
use pocketmine\block\EndPortalFrame;
use pocketmine\block\Water;
use pocketmine\color\Color;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\Config;
use pocketmine\world\particle\DustParticle;

class footballEntity extends Human
{

    /** @var int */
    private $airTicks = 0;

    /** @var float */
    public $width = 0.4;
    /** @var float */
    public $height = 0.4;

    /**
     * @param int $currentTick
     * @return bool
     */

    public function __construct(Location $location, Skin $skin, ?CompoundTag $nbt = null)
    {
        parent::__construct($location, $skin, $nbt);
        return true;
    }

    public function onUpdate(int $currentTick): bool
    {
        if ($this->isClosed()) {
            return false;
        }
        $this->getWorld()->addParticle($this->getPosition(), new DustParticle(new Color(mt_rand(0,255), mt_rand(0,255), mt_rand(0,255))));

        foreach ($this->getWorld()->getNearbyEntities($this->boundingBox->expandedCopy(0.2, 0.2, 0.2), $this) as $player) {
            if ($player instanceof Player) {
                $this->setRotation($player->getLocation()->getYaw(), 0);
                if ($player->isSprinting()) {
                    $this->setMotion(new Vector3($player->getDirectionVector()->x * 0.1, 0.5, $player->getDirectionVector()->z * 1.2));
                } elseif ($player->isSneaking()) {
                    $this->setMotion(new Vector3($player->getDirectionVector()->x / 1.5, 0.5, $player->getDirectionVector()->z / 2));
                } else {
                    $this->setMotion(new Vector3($player->getDirectionVector()->x, 0.5, $player->getDirectionVector()->z));
                }
               // $this->getWorld()->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_ITEM_SHIELD_BLOCK);

            }
        }

        if (!$this->isOnGround()) {
            $this->airTicks++;
        }

     /*   if (math::getFrontBlock($this)->isSolid() or math::getFrontBlock($this) instanceof Cobweb) {
            $yaw = $this->getLocation()->yaw - 180;
            if ($yaw < 0) {
                $yaw += 360;
            }
            $this->setRotation($yaw, 0);
            $this->setMotion(new Vector3($this->getDirectionVector()->x * $this->motion->x, 0.5, $this->getDirectionVector()->z * $this->motion->z));

            $this->airTicks = 5;
        }*/

        $blockU = $this->getWorld()->getBlockAt((int)floor($this->getPosition()->x), (int)floor($this->getPosition()->y) - 1, (int)floor($this->getPosition()->z));
        if ($blockU->isSolid() && $this->airTicks > 10) {
            $this->setMotion(new Vector3($this->motion->x * 1.1, $this->airTicks / 30, $this->motion->z * 1.1));
            $this->airTicks = 0;
        }

        if ($this->getWorld()->getBlockAt((int)floor($this->getPosition()->x), (int)floor($this->getPosition()->y), (int)floor($this->getPosition()->z)) instanceof Water) {
            $this->setMotion(new Vector3($this->motion->x / 10, 0.16, $this->motion->z / 10));
        }

        if ($this->isOnFire()) {
            $this->flagForDespawn();

            $pk = new PlaySoundPacket();
            $pk->x = $this->getPosition()->x;
            $pk->y = $this->getPosition()->y;
            $pk->z = $this->getPosition()->z;
            $pk->soundName = "random.explode";
            $pk->volume = 1;
            $pk->pitch = 1;
            $this->getWorld()->broadcastPacketToViewers($this->getPosition(), $pk);
        }

        $this->setScale(1.5);

        return parent::onUpdate($currentTick);
    }


    public function attack(EntityDamageEvent $source): void
    {
        if ($source instanceof EntityDamageByEntityEvent) {
            $damager = $source->getDamager();
            $this->setRotation($damager->getLocation()->getYaw(), 0);
            $this->setMotion(new Vector3($damager->getDirectionVector()->x, 0.7, $damager->getDirectionVector()->z));
           // $this->getLevel()->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_ITEM_SHIELD_BLOCK);
        }


    }




}