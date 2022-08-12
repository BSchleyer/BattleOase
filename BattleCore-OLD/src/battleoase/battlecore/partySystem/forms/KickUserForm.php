<?php

namespace battleoase\battlecore\partySystem\forms;

use battleoase\battlecore\partySystem\PartySystem;
use battleoase\battlecore\partySystem\utils\Utils;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Input;
use pocketmine\player\Player;
use pocketmine\Server;

class KickUserForm extends CustomForm {

    public function __construct()
    {
        $title = "§eKick Player";
        $elements = [
            new Input("input", "Hurensohn"),
        ];
        parent::__construct($title, $elements, function (Player $player, CustomFormResponse $response): void{
            $name = $response->getString("input");
            $p = Server::getInstance()->getPlayerExact($name);
            if ($p != null) {
                if (PartySystem::getDatabase()->isInParty($p->getName())) {
                    if (PartySystem::getDatabase()->getParty($player->getName())["party_name"] === PartySystem::getDatabase()->getParty($p->getName())["party_name"]){
                        Utils::kickMember($p->getName(), PartySystem::getDatabase()->getParty($player->getName())["party_name"], $player->getName());
                    }
                } else {
                    $player->sendMessage("§cThis player is already in an party.");
                }
            }
        });
    }
}