<?php

namespace battleoase\battlecore\gamePassSystem\form\admin;

use battleoase\battlecore\gamePassSystem\GamePassSystem;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Dropdown;
use dktapps\pmforms\element\Toggle;
use pocketmine\player\Player;

class AdminGamePassSettingsForm extends CustomForm {

    public function __construct()
    {
        parent::__construct(GamePassSystem::TITLE, [
            new Toggle("§cGamePass maintenance", "GamePass maintenance", false),
            new Dropdown("Seasons", "Seasons", [
                "Season 1",
                "Season 2",
                "Season 3",
                "Season 4",
                "Season 5",
                "Season 6",
                "Season 7",
                "Season 8"
            ])
        ], function(Player $player, CustomFormResponse $response) : void{
                $player->sendMessage(print_r($response, true));
        }, function (Player $player): void {
            $player->sendForm(new AdminGamePassForm());
        });
    }

}