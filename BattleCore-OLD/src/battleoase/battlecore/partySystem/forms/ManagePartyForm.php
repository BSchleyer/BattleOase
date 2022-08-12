<?php

namespace battleoase\battlecore\partySystem\forms;

use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;

class ManagePartyForm extends MenuForm {

    public function __construct()
    {
        $title = "§cManage Party";
        $text = "§cManage your party.";
        $elements = [
            new MenuOption("Invite player"),
            new MenuOption("Kick player"),
            new MenuOption("Party settings"),
            new MenuOption("Delete party"),
        ];
        parent::__construct($title, $text, $elements, function (Player $player, $data): void{
            if ($data == 0){
                $player->sendForm(new InviteUserForm());
            } elseif ($data == 1){
                $player->sendForm(new KickUserForm());
            } elseif ($data == 2){
                //Party Settings form
            } elseif ($data == 3){
                //Delete party
            }
        });
    }

}