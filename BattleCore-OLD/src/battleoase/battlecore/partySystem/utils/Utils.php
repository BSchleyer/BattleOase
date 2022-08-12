<?php

namespace battleoase\battlecore\partySystem\utils;

use battleoase\battlecore\partySystem\events\PartyJoinEvent;
use battleoase\battlecore\partySystem\events\PartyLeaveEvent;
use battleoase\battlecore\partySystem\events\PlayerPartyKickEvent;

final class Utils {

    static function getPartyMembers(): array{
        return []; //ToDo: get members from Database
    }

    static function addMember(string $name, string $partyName){
        $ev = new PartyJoinEvent($name, $partyName);
        $ev->call();
    }

    static function removeMember(string $name, string $partyName){
        $ev = new PartyLeaveEvent($name, $partyName);
        $ev->call();
    }

    static function kickMember(string $name, string $partyName, string $kickedBy){
        $ev = new PlayerPartyKickEvent($name, $partyName, $kickedBy);
        $ev->call();
    }

    /**
     * Function encodeJson
     * @param array $array
     * @return string
     */
    public function encodeJson(array $array): string{
        return json_encode($array);
    }

    /**
     * Function decodeJson
     * @param string $string
     * @return array
     */
    public function decodeJson(string $string): array{
        return json_decode($string) ?? [];
    }
}