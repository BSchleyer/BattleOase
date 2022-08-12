<?php

namespace battleoase\battlecore\partySystem\forms;

use battleoase\battlecore\partySystem\PartySystem;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Input;
use pocketmine\player\Player;
use pocketmine\Server;

class InviteUserForm extends CustomForm {

    public function __construct()
    {
        $title = "§eInvite Player";
        $elements = [
            new Input("input", "Hurensohn"),
        ];
        parent::__construct($title, $elements, function (Player $player, CustomFormResponse $response): void{
            $name = $response->getString("input");
            $p = Server::getInstance()->getPlayerExact($name);
            if ($p != null) {
                if (!PartySystem::getDatabase()->isInParty($p->getName())) {
                    if (!PartySystem::getDatabase()->isPartyRequest($p->getName(), "{$player->getName()}_Party")) {
                        PartySystem::getDatabase()->addPartyRequest($p->getName(), "{$player->getName()}_Party");
                    } else {
                        $player->sendMessage("§cYou have already sent an party request to this player.");
                    }
                } else {
                    $player->sendMessage("§cThis player is already in an party.");
                }
            }
        });
    }
}