<?php

namespace battleoase\battlecore\partySystem\listener;

use battleoase\battlecore\partySystem\events\PartyJoinEvent;
use battleoase\battlecore\partySystem\events\PartyLeaveEvent;
use battleoase\battlecore\partySystem\events\PlayerPartyKickEvent;
use battleoase\battlecore\partySystem\PartySystem;
use JetBrains\PhpStorm\Pure;
use pocketmine\event\Listener;
use pocketmine\Server;

class EventListener implements Listener {

    /**
     * @throws \Exception
     */
    function onPartyJoin(PartyJoinEvent $event){
        $player = $event->getPlayer();
        $party = $event->getPartyName();

        PartySystem::getDatabase()->joinParty($player, $party);
        PartySystem::getDatabase()->removePartyRequest($player, $party);
    }

    /**
     * @throws \Exception
     */
    function onPartyLeave(PartyLeaveEvent $event){
        $player = $event->getPlayer();
        $party = $event->getPartyName();

        PartySystem::getDatabase()->leaveParty($player, $party);
    }

    /**
     * @throws \Exception
     */
    function onPlayerPartyKick(PlayerPartyKickEvent $event){
        $player = $event->getPlayer();
        $party = $event->getPartyName();
        $kickedBy = $event->getKickedBy();

        $p = Server::getInstance()->getPlayerExact($player);
        $p?->sendMessage("§cYou was kicked out of the party by §e{$kickedBy}§8.");
        PartySystem::getDatabase()->leaveParty($player, $party);
    }
}