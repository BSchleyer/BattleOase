<?php

namespace battleoase\battlecore\replaySystemPlayer\scheduler;

use battleoase\battlecore\BattleCore;
use battleoase\battlecore\replaySystemPlayer\entities\HumanEntity;
use battleoase\battlecore\replaySystemPlayer\manager\Replay;
use battleoase\battlecore\replaySystemPlayer\ReplaySystemPlayer;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\object\ExperienceOrb;
use pocketmine\entity\object\ItemEntity;
use pocketmine\entity\Skin;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\ByteArrayTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\LongTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\MobArmorEquipmentPacket;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class PlayReplayTask extends Task {

    /** @var Replay */
    private Replay $replay;

    /** @var int */
    private int $currentTick;

    /**
     * PlayReplayTask constructor.
     * @param Replay $replay
     * @param int $currentTick
     */

    public function __construct(Replay $replay, int $currentTick = 0) {
        $this->replay = $replay;
        $this->currentTick = $currentTick;
    }

    public function onRun(): void {
        $replay = $this->replay;
        $tick = $this->currentTick;

        $seconds = floor($tick / 20);
        $minutes = floor($seconds / 60);
        $seconds = floor($seconds % 60);
        if($seconds < 10) $seconds = "0" . $seconds;
        if($minutes < 10) $minutes = "0" . $minutes;
        $rseconds = floor($replay->getLastTick() / 20);
        $rminutes = floor($rseconds / 60);
        $rseconds = floor($rseconds % 60);
        if($rseconds < 10) $rseconds = "0" . $rseconds;
        if($rminutes < 10) $rminutes = "0" . $rminutes;

            Server::getInstance()->broadcastTip(($this->replay->isPaused() ? "§r§7[§a§l PAUSE §r§7]§r" : "§r§7[§a" . $minutes . "§7:§a" . $seconds . "§7 / §a" . $rminutes . "§7:§a" . $rseconds . "§7]"));
        if($tick >= $replay->getLastTick() && $replay->getNextTick() >= $replay->getLastTick()){
            BattleCore::getInstance()->getScheduler()->scheduleDelayedTask(new PlayReplayTask($replay, $tick), 1);
            return;
        }
        if($replay->isPaused() && ($tick + 1) === $replay->getNextTick()) {
			BattleCore::getInstance()->getScheduler()->scheduleDelayedTask(new PlayReplayTask($replay, $tick), 1);
            return;
        }
        $tick = ++$this->currentTick;
        if($tick !== $replay->getNextTick() ) {
            if($replay->getNextTick() > $tick) {
                $ticks = ($replay->getNextTick() - $tick) + $tick;
                for ($tempTick = $tick; $tempTick <= $ticks; $tempTick++) {
                    $this->doActions($tempTick, $replay, Replay::REPLAY_NORMAL);
                    $tick = $tempTick;
                }
            } else {
                $ticks = $tick - ($tick - $replay->getNextTick());
                for ($tempTick = $tick; $tempTick >= $ticks; $tempTick--) {
                    $this->doActions($tempTick, $replay, Replay::REPLAY_BACKWARDS);
                    $tick = $tempTick;
                }
            }
        } else {
            $this->doActions($tick, $replay);
        }
        $replay->currentTick = $tick;
        $this->currentTick = $tick;
        $replay->setNextTick($tick + 1);

		BattleCore::getInstance()->getScheduler()->scheduleDelayedTask(new PlayReplayTask($replay, $tick), 1);
    }

    /**
     * @param int $tick
     * @param Replay $replay
     * @param $type
     */

    public function doActions(int $tick, Replay $replay, $type = Replay::REPLAY_NORMAL): void {
        $actions = $replay->getActions();
        $entities = $replay->getEntities();
        $replay->getLevel()->setTime(0);
        //Spawn Entity "Action" (It´s no Action but yeaahhhh :-D)
        if(isset($entities[$tick])){
            $entities = $entities[$tick];
            foreach ($entities as $entity) {

                if($type === Replay::REPLAY_BACKWARDS) {
                    continue;
                }
                if(isset($replay->spawnedEntitiesFakeId[$entity["Id"]])) {
                    continue;
                }

                $replay->getLevel()->loadChunk((float)$entity["X"], (float)$entity["Z"]);
                switch ($entity["NetworkID"]){
                    case -1;
                        $nbt = null;
                        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                            $nbt = Entity::createBaseNBT($player->asVector3(), null, (float)$entity["Yaw"], (float)$entity["Pitch"]);
                        }
                        $skinTag = null;
                        if(isset($entity["Skin"]) && !is_null(unserialize($entity["Skin"]))) {
                            $skinTag = unserialize($entity["Skin"]);
                        } else {
                            foreach (Server::getInstance()->getOnlinePlayers() as $player){
                                $skinTag = $player->namedtag->getCompoundTag("Skin");
                            }
                        }
                        $nbt->setTag($skinTag);
                        $human = new HumanEntity($replay->getLevel(), $nbt);
                        $human->setNameTagAlwaysVisible();
                        $human->setNameTag($entity["Nametag"]);
                        $human->spawnToAll();
                        $human->teleport(new Vector3((float)$entity["X"], (float)$entity["Y"], (float)$entity["Z"]));

                        //todo: fix this
                        $replay->spawnedEntitiesFakeId[$entity["Id"]] = $human->getId();
                        $replay->spawnedEntitiesRealId[$human->getId()] = $entity["Id"];

                        /*
                        $skinTag = $nbt->getCompoundTag("Skin");
                        if($skinTag === null){
                            break;
                        }
                        $skin = new Skin($skinTag->getString("Name"), $skinTag->hasTag("Data", StringTag::class) ? $skinTag->getString("Data") : $skinTag->getByteArray("Data"), $skinTag->getByteArray("CapeData", ""), $skinTag->getString("GeometryName", ""), $skinTag->getByteArray("GeometryData", ""));
                        $human->setSkin($skin);*/
                        break;
                    case 64:
                        $nbt = Entity::createBaseNBT(new Vector3((float)$entity["X"], (float)$entity["Y"], (float)$entity["Z"]), null, (float)$entity["Yaw"], (float)$entity["Pitch"]);
                        $item = new Item($entity["ItemId"], $entity["ItemMeta"]);
                        $itemTag = $item->nbtSerialize();
                        $itemTag->setName("Item");
                        $nbt->setTag($itemTag);
                        $itemEntity = new ItemEntity($replay->getLevel(), $nbt);
                        $itemEntity->spawnToAll();
                        $replay->spawnedEntitiesFakeId[$entity["Id"]] = $itemEntity->getId();
                        $replay->spawnedEntitiesRealId[$itemEntity->getId()] = $entity["Id"];
                        break;
                    case 69: {
                        /*
                        $nbt = Entity::createBaseNBT(new Vector3((float)$entity["X"], (float)$entity["Y"], (float)$entity["Z"]), null, (float)$entity["Yaw"], (float)$entity["Pitch"]);
                        $nbt->setShort(ExperienceOrb::TAG_VALUE_PC, 10);
                        $nbt->setInt(ExperienceOrb::TAG_VALUE_PE, 10);
                        $spawnEntity = new ExperienceOrb($replay->getLevel(), $nbt);
                        $spawnEntity->spawnToAll();
                        $replay->spawnedEntitiesFakeId[$entity["Id"]] = $spawnEntity->getId();
                        $replay->spawnedEntitiesRealId[$spawnEntity->getId()] = $entity["Id"];
                        */
                        break;
                    }
                    case 66: {}
                    case 84: {
                        break;
                    }
                    case 81: {
                        new BattleTask(2, function (int $tick) use ($entity, $replay): void {
                            $networkId = $entity["NetworkID"];
                            $nbt = Entity::createBaseNBT(new Vector3((float)$entity["X"], (float)$entity["Y"], (float)$entity["Z"]), null, (float)$entity["Yaw"], (float)$entity["Pitch"]);
                            $spawnEntity = Entity::createEntity("Snowball", $replay->getLevel(), $nbt);
                            $spawnEntity->setNameTagAlwaysVisible();
                            $spawnEntity->spawnToAll();
                            $replay->spawnedEntitiesFakeId[$entity["Id"]] = $spawnEntity->getId();
                            $replay->spawnedEntitiesRealId[$spawnEntity->getId()] = $entity["Id"];
                        });
                        break;
                    }
                    case 80: {
                        new BattleTask(2, function (int $tick) use ($entity, $replay): void {
                            $networkId = $entity["NetworkID"];
                            $nbt = Entity::createBaseNBT(new Vector3((float)$entity["X"], (float)$entity["Y"], (float)$entity["Z"]), null, (float)$entity["Yaw"], (float)$entity["Pitch"]);
                            $spawnEntity = Entity::createEntity("Arrow", $replay->getLevel(), $nbt);
                            $spawnEntity->setNameTagAlwaysVisible();
                            $spawnEntity->spawnToAll();
                            $replay->spawnedEntitiesFakeId[$entity["Id"]] = $spawnEntity->getId();
                            $replay->spawnedEntitiesRealId[$spawnEntity->getId()] = $entity["Id"];
                        });
                        break;
                    }
                    default:
                        $networkId = $entity["NetworkID"];
                        $nbt = Entity::createBaseNBT(new Vector3((float)$entity["X"], (float)$entity["Y"], (float)$entity["Z"]), null, (float)$entity["Yaw"], (float)$entity["Pitch"]);
                        $spawnEntity = Entity::createEntity($networkId, $replay->getLevel(), $nbt);
                        $spawnEntity->setNameTagAlwaysVisible();
                        $spawnEntity->spawnToAll();
                        $replay->spawnedEntitiesFakeId[$entity["Id"]] = $spawnEntity->getId();
                        $replay->spawnedEntitiesRealId[$spawnEntity->getId()] = $entity["Id"];
                }
            }
        }

        //Place Action
        if(isset($actions["Place"])){
            if(isset($actions["Place"][$tick])){
                $datas = $actions["Place"][$tick];
                foreach ($datas as $data) {
                    switch ($type) {
                        case Replay::REPLAY_BACKWARDS: {
                            if($tick >= 10) {
                                $replay->getLevel()->setBlockIdAt((int)$data["X"], (int)$data["Y"], (int)$data["Z"], 0);
                            }
                            break;
                        }
                        default: {
                            $replay->getLevel()->setBlockIdAt((int)$data["X"], (int)$data["Y"], (int)$data["Z"], (int)$data["Id"]);
                            $replay->getLevel()->setBlockDataAt((int)$data["X"], (int)$data["Y"], (int)$data["Z"], (int)$data["Meta"]);
                        }
                    }
                }
            }
        }

        //BlockUpdate Action
        if(isset($actions["BlockUpdate"])){
            if(isset($actions["BlockUpdate"][$tick])){
                $datas = $actions["BlockUpdate"][$tick];
                foreach ($datas as $data) {
                    $replay->getLevel()->getBlockAt((int)$data["X"], (int)$data["Y"], (int)$data["Z"])->onNearbyBlockChange();
                }
            }
        }

        //Break Action
        if(isset($actions["Break"])){
            if(isset($actions["Break"][$tick])){
                $datas = $actions["Break"][$tick];
                foreach ($datas as $data) {
                    switch ($type) {
                        case Replay::REPLAY_BACKWARDS: {
                            $replay->getLevel()->setBlockIdAt((int)$data["X"], (int)$data["Y"], (int)$data["Z"], (int)$data["Id"]);
                            $replay->getLevel()->setBlockDataAt((int)$data["X"], (int)$data["Y"], (int)$data["Z"], (int)$data["Meta"]);
                            break;
                        }
                        default: {
                            $replay->getLevel()->setBlockIdAt($data["X"], $data["Y"], $data["Z"], 0);
                            if(!$data["Silent"]){
                                $replay->getLevel()->addParticle(new DestroyBlockParticle(new Vector3($data["X"], $data["Y"], $data["Z"]), new Block($data["Id"], $data["Meta"])));
                            }
                        }
                    }
                }
            }
        }

        //Move Action
        if(isset($actions["Move"]) && isset($actions["Move"][$tick])){
            foreach ($replay->getLevel()->getEntities() as $entity){
                if(!$entity instanceof Player){
                    if(isset($replay->spawnedEntitiesRealId[$entity->getId()])){
                        $entityId = $replay->spawnedEntitiesRealId[$entity->getId()];
                        if(isset($actions["Move"][$tick][$entityId])){
                            $datas = $actions["Move"][$tick][$entityId];
                            foreach ($datas as $data) {
                                $motion = new Vector3(
                                    (float)$data["X"] - $entity->x,
                                    (float)$data["Y"] - $entity->y,
                                    (float)$data["Z"] - $entity->z
                                );
                                switch ($type) {
                                    case Replay::REPLAY_BACKWARDS: {
                                        $motion = new Vector3(
                                            $entity->x - (float)$data["X"],
                                            $entity->y - (float)$data["Y"],
                                            $entity->z - (float)$data["Z"]
                                        );
                                        break;
                                    }
                                }
                                $entity->setMotion($motion);
                                $entity->setRotation((float)$data["Yaw"], (float)$data["Pitch"]);
                                if($entity instanceof HumanEntity) {
                                    $entity->needsUpdate = true;
                                    if(isset($data["OnFire"])) {
                                        if($data["OnFire"]) {
                                            $entity->setOnFire(60);
                                        } else {
                                            $entity->extinguish();
                                        }
                                    }
                                }
                                if($entity->distance(new Vector3((float)$data["X"], (float)$data["Y"], (float)$data["Z"])) >= 4) {
                                    $entity->teleport(new Vector3((float)$data["X"], (float)$data["Y"], (float)$data["Z"]));
                                }
                                if($entity instanceof HumanEntity) {
                                    if(isset($data["ID"])) {
                                        if (!($entity->getInventory()->getItemInHand()->getId() === $data["ID"])) {
                                            $entity->getInventory()->setItemInHand(Item::get($data["ID"]));
                                            $entity->getInventory()->sendHeldItem($entity->getViewers());

                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        //Update Entity Action
        if(isset($actions["UpdateEntity"]) && isset($actions["UpdateEntity"][$tick])){
            foreach ($replay->getLevel()->getEntities() as $entity){
                if(!$entity instanceof Player){
                    if(isset($replay->spawnedEntitiesRealId[$entity->getId()])){
                        $entityId = $replay->spawnedEntitiesRealId[$entity->getId()];
                        if(isset($actions["UpdateEntity"][$tick][$entityId])){
                            $datas = $actions["UpdateEntity"][$tick][$entityId];
                            foreach ($datas as $data) {
                                $entity->setNameTag($data["Nametag"]);
                                $entity->setScale($data["Scale"]);

                                if(!empty($entity->getNameTag())) {
                                    $entity->setNameTagVisible();
                                    $entity->setNameTagAlwaysVisible();
                                }
                            }
                        }
                    }
                }
            }
        }

        //Animate Action
        if(isset($actions["Animation"]) && isset($actions["Animation"][$tick])){
            foreach ($replay->getLevel()->getEntities() as $entity){
                if(!$entity instanceof Player){
                    if(isset($replay->spawnedEntitiesRealId[$entity->getId()])){
                        $entityId = $replay->spawnedEntitiesRealId[$entity->getId()];
                        if(isset($actions["Animation"][$tick][$entityId])){
                            $datas = $actions["Animation"][$tick][$entityId];
                            foreach ($datas as $data) {
                                $pk = new AnimatePacket();
                                $pk->entityRuntimeId = $entity->getId();
                                $pk->action = (int) $data["Type"];
                                $replay->getLevel()->broadcastPacketToViewers($entity, $pk);
                            }
                        }
                    }
                }
            }
        }

        //Damage Action
        if(isset($actions["Damage"]) && isset($actions["Damage"][$tick])){
            foreach ($replay->getLevel()->getEntities() as $entity){
                if(!$entity instanceof Player){
                    if(isset($replay->spawnedEntitiesRealId[$entity->getId()])){
                        $entityId = $replay->spawnedEntitiesRealId[$entity->getId()];
                        if(isset($actions["Damage"][$tick][$entityId])){
                            foreach ($actions["Damage"][$tick][$entityId] as $action) {
                                $entity->broadcastEntityEvent(ActorEventPacket::HURT_ANIMATION);
                            }
                        }
                    }
                }
            }
        }

        //Sneak Action
        if(isset($actions["Sneak"]) && isset($actions["Sneak"][$tick])){
            foreach ($replay->getLevel()->getEntities() as $entity){
                if(!$entity instanceof Player){
                    if(isset($replay->spawnedEntitiesRealId[$entity->getId()])){
                        $entityId = $replay->spawnedEntitiesRealId[$entity->getId()];
                        if(isset($actions["Sneak"][$tick][$entityId])){
                            $datas = $actions["Sneak"][$tick][$entityId];
                            foreach ($datas as $data) {
                                switch ($type) {
                                    case Replay::REPLAY_BACKWARDS: {
                                        $entity->setSneaking(!$data["Sneaking"]);
                                        break;
                                    }
                                    default: {
                                        $entity->setSneaking($data["Sneaking"]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        //Consume Item Action
        if(isset($actions["Consume"]) && isset($actions["Consume"][$tick])){
            foreach ($replay->getLevel()->getEntities() as $entity){
                if(!$entity instanceof Player){
                    if(isset($replay->spawnedEntitiesRealId[$entity->getId()])){
                        $entityId = $replay->spawnedEntitiesRealId[$entity->getId()];
                        if(isset($actions["Consume"][$tick][$entityId])){
                            $datas = $actions["Consume"][$tick][$entityId];
                            foreach ($datas as $data) {
                                $entity->broadcastEntityEvent(ActorEventPacket::EATING_ITEM);
                            }
                        }
                    }
                }
            }
        }

        //Quit Action
        if(isset($actions["Quit"][$tick])){
            foreach ($actions["Quit"][$tick] as $eId) {
                foreach ($replay->getLevel()->getEntities() as $entity){
                    if(!$entity instanceof Player){
                        if(isset($replay->spawnedEntitiesRealId[$entity->getId()])){
                            $entityId = $replay->spawnedEntitiesRealId[$entity->getId()];
                            if($eId === $entityId) {
                                $entity->flagForDespawn();
                            }
                        }
                    }
                }
            }
        }

        //Death Action
        if(isset($actions["Death"]) && isset($actions["Death"][$tick])){
            foreach ($replay->getLevel()->getEntities() as $entity){
                if(!$entity instanceof Player){
                    if(isset($replay->spawnedEntitiesRealId[$entity->getId()])){
                        $entityId = $replay->spawnedEntitiesRealId[$entity->getId()];
                        if(isset($actions["Death"][$tick][$entityId])){
                            $entity->broadcastEntityEvent(ActorEventPacket::DEATH_ANIMATION);
                            $entity->broadcastEntityEvent(ActorEventPacket::RESPAWN);
                        }
                    }
                }
            }
        }

        //Despawn Entity Action
        if(isset($actions["DespawnEntity"]) && isset($actions["DespawnEntity"][$tick])){
            foreach ($actions["DespawnEntity"][$tick] as $eId) {
                foreach ($replay->getLevel()->getEntities() as $entity){
                    if(!$entity instanceof Player){
                        if(isset($replay->spawnedEntitiesRealId[$entity->getId()])){
                            $entityId = $replay->spawnedEntitiesRealId[$entity->getId()];
                            if($type === Replay::REPLAY_BACKWARDS) {
                                return;
                            }
                            if($eId === $entityId){
                                $entity->flagForDespawn();
                            }
                        }
                    }
                }
            }
        }

        //Sign Change Action
        if(isset($actions["SignChange"]) && isset($actions["SignChange"][$tick])){
            $data = $actions["SignChange"][$tick];
            $tile = $replay->getLevel()->getTile(new Vector3($data["X"], $data["Y"], $data["Z"]));
            if(!$tile === null && $tile instanceof Sign){
                $tile->setLine(0, $data["Line1"]);
                $tile->setLine(1, $data["Line2"]);
                $tile->setLine(2, $data["Line3"]);
                $tile->setLine(3, $data["Line4"]);
            }
        }

        //Update HeldItem Action

        if(isset($actions["ItemHeldUpdate"]) && isset($actions["ItemHeldUpdate"][$tick])){
            foreach ($replay->getLevel()->getEntities() as $entity){
                if(!$entity instanceof Player){
                    if(isset($replay->spawnedEntitiesRealId[$entity->getId()])){
                        $entityId = $replay->spawnedEntitiesRealId[$entity->getId()];
                        if(isset($actions["ItemHeldUpdate"][$tick][$entityId])){
                            $data = $actions["ItemHeldUpdate"][$tick][$entityId];
                            if($entity instanceof Human){
                                $pk = new MobEquipmentPacket();
                                $pk->entityRuntimeId = $entity->getId();
                                $item = new Item((int)$data["ItemId"], (int)$data["ItemMeta"]);
                                if(isset($data["Enchanted"]) && (bool)$data["Enchanted"]) {
                                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING)));
                                }
                                $pk->item = $item;
                                $pk->hotbarSlot = $entity->getInventory()->getHeldItemIndex();
                                $pk->inventorySlot = $entity->getInventory()->getHeldItemIndex();
                              //  Server::getInstance()->broadcastPacket(Server::getInstance()->getLoggedInPlayers(), $pk);

                                if(isset($data["Armor"])) {
                                    $pk = new MobArmorEquipmentPacket();
                                    $pk->entityRuntimeId = $entity->getId();

                                    $boots = new Item((int)explode(":", $data["Armor"]["Boots"])[0], (int)explode(":", $data["Armor"]["Boots"])[1]);
                                    if(isset(explode(":", $data["Armor"]["Boots"])[2]) && (bool)explode(":", $data["Armor"]["Boots"])[2]) {
                                        $boots->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING)));
                                    }
                                    $pk->feet = $boots;

                                    $legs = new Item((int)explode(":", $data["Armor"]["Leggings"])[0], (int)explode(":", $data["Armor"]["Leggings"])[1]);
                                    if(isset(explode(":", $data["Armor"]["Leggings"])[2]) && (bool)explode(":", $data["Armor"]["Leggings"])[2]) {
                                        $legs->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING)));
                                    }
                                    $pk->legs = $legs;

                                    $chest = new Item((int)explode(":", $data["Armor"]["Chestplate"])[0], (int)explode(":", $data["Armor"]["Chestplate"])[1]);
                                    if(isset(explode(":", $data["Armor"]["Chestplate"])[2]) && (bool)explode(":", $data["Armor"]["Chestplate"])[2]) {
                                        $chest->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING)));
                                    }
                                    $pk->chest = $chest;

                                    $head = new Item((int)explode(":", $data["Armor"]["Helmet"])[0], (int)explode(":", $data["Armor"]["Helmet"])[1]);
                                    if(isset(explode(":", $data["Armor"]["Helmet"])[2]) && (bool)explode(":", $data["Armor"]["Helmet"])[2]) {
                                        $head->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING)));
                                    }
                                    $pk->head = $head;
       //                             Server::getInstance()->broadcastPacket(Server::getInstance()->getLoggedInPlayers(), $pk);
                                }
                            }
                        }
                    }
                }
            }
        }

        //cgha
        if(isset($actions["Chat"]) && isset($actions["Chat"][$tick])){
            foreach ($actions["Chat"][$tick] as $data) {
                if($type !== Replay::REPLAY_BACKWARDS) {
                    Server::getInstance()->broadcastMessage($data["Message"]);
                }
            }
        }

        //LevelEvent Action
        if(isset($actions["LevelEventAction"]) && isset($actions["LevelEventAction"][$tick])){
            foreach ($actions["LevelEventAction"][$tick] as $data) {
                if($type !== Replay::REPLAY_BACKWARDS) {
                    $replay->getLevel()->broadcastLevelEvent(Math::stringVectorToVector3($data["Position"], ":"), $data["EventId"], $data["Data"]);
                }
            }
        }
    }
}