<?php

namespace battleoase\battlecore\npcSystem;

use battleoase\battlecore\BattleCore;
use battleoase\battlecore\npcSystem\entities\NPCEntity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;

class NpcSystem {

    public function __construct() {
        EntityFactory::getInstance()->register(NPCEntity::class, function (World $world, CompoundTag $nbt): NPCEntity {
            return new NPCEntity(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt, true);
        }, ['npcentity', 'npcentity:base']);
    }

    public static function spawn(string $skinName, string $nametag, Location $location, bool $loockAtPlayer = false, \Closure $closure = null, \Closure $extraData = null, $emote = []) {
        $npc = new NPCEntity($location, BattleCore::getInstance()->statsSystem->getSkin($skinName));
        $npc->setLookAtPlayer($loockAtPlayer);
        $npc->setAttackClosure($closure);
        $npc->setInteractClosure($closure);
        $npc->setEmotes($emote);
        $npc->setNameTag($nametag);
        $npc->setCanSaveWithChunk(false);
        if($extraData !== null) {
            $extraData($npc);
        }
        $npc->spawnToAll();
    }

}