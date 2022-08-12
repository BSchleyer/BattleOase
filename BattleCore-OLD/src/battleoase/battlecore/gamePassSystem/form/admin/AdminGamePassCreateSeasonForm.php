<?php

namespace battleoase\battlecore\gamePassSystem\form\admin;

use battleoase\battlecore\gamePassSystem\GamePassSystem;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Dropdown;
use dktapps\pmforms\element\Input;
use dktapps\pmforms\element\Toggle;
use pocketmine\player\Player;

class AdminGamePassCreateSeasonForm extends CustomForm {

    public function __construct()
    {
        parent::__construct(GamePassSystem::TITLE, [
            new Input("Title", "Season Name", "Season Name"),
        ], function(Player $player, CustomFormResponse $response) : void{
            $player->sendMessage(print_r($response, true));
        }, function (Player $player): void {
            $player->sendForm(new AdminGamePassForm());
        });
    }

}