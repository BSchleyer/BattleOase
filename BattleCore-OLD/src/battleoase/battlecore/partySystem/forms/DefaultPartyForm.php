<?php

namespace battleoase\battlecore\partySystem\forms;

use battleoase\battlecore\partySystem\PartySystem;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;

class DefaultPartyForm extends MenuForm {

    public function __construct()
    {
        $title = "§5Party";
        $text = "Manage your Parties";
        $elements = [
            new MenuOption("§aCreate Party"),
            new MenuOption("§eRequests"),
            new MenuOption("§cPublic Parties"),
            new MenuOption("§4Settings"),
        ];
        parent::__construct($title, $text, $elements, function (Player $player, $data): void{
            if ($data == 0){
                PartySystem::getDatabase()->createParty($player);
            } elseif ($data == 1){
                //Request Form
            } elseif ($data == 2){
                //Public Parties
            } elseif ($data == 3){
                //User settings
            }
        });
    }

}