<?php

namespace battleoase\battlecore\npcSystem\handler;

use battleoase\battlecore\BattleCore;
use battleoase\battlecore\npcSystem\entities\CustomNPC;
use battleoase\battlecore\npcSystem\handler\preset\EditNpcHandler;
use battleoase\battlecore\statsSystem\StatsSystem;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\player\Player;
use pocketmine\timings\Timings;
use pocketmine\timings\TimingsHandler;

class EventListener implements Listener
{

    public function onPlayerJoin(PlayerJoinEvent $event)
    {
        BattleCore::getInstance()->statsSystem->saveSkin($event->getPlayer(), $event->getPlayer()->getSkin());
    }

    public function onHit(EntityDamageByEntityEvent $event)
    {
        $entity = $event->getEntity();
        $damager = $event->getDamager();
        if ($entity instanceof CustomNPC) {
            if ($damager instanceof Player) {
                if ($damager->isSneaking()) {
                    (new EditNpcHandler())->onHit($entity, $event);
                } else {
                    if ($entity->hasHandler()) {
                        if (!$entity->getHandler()->onHit($entity, $event)) {
                            $event->cancel();
                        }
                    }
                }
            }
        }
    }
}