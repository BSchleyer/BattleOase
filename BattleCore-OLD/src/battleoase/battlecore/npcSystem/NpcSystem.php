<?php

namespace battleoase\battlecore\npcSystem;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\npcSystem\commands\NpcCommand;
use battleoase\battlecore\npcSystem\entities\CustomNPC;
use battleoase\battlecore\npcSystem\entities\NPCEntity;
use battleoase\battlecore\npcSystem\handler\EventListener;
use battleoase\battlecore\npcSystem\utils\ConfigLoader;
use battleoase\battlecore\statsSystem\StatsSystem;
use battleoase\battlecore\utils\BPlugin;
use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;

class NpcSystem extends BPlugin{

    public function __construct()
    {
        $loader = new ConfigLoader();
        $loader->load("/home/cloud/data/npcs/");
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this->getPlugin());
        $this->getServer()->getCommandMap()->register("npc", new NpcCommand());
        EntityFactory::getInstance()->register(CustomNPC::class, function (World $world, CompoundTag $nbt): CustomNPC {
            return new CustomNPC(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt, true);
        }, ['BATTLENPC', 'battlenpc:base']);
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
