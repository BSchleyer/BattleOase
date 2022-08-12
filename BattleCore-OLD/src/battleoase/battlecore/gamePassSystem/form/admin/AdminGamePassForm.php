<?php

namespace battleoase\battlecore\gamePassSystem\form\admin;

use battleoase\battlecore\gamePassSystem\GamePassSystem;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;

class AdminGamePassForm extends MenuForm {

    public function __construct() {

        parent::__construct(GamePassSystem::TITLE, "", [
            new MenuOption("§7Settings"),
            new MenuOption("§aCreate Season"),
            new MenuOption("§aCreate Level"),
            new MenuOption("§eEdit Level"),
            new MenuOption("§cRemove Level"),
        ], function (Player $player, int $data): void{
            match ($data) {
                0 => $player->sendForm(new AdminGamePassSettingsForm()),
                1 => $player->sendForm(new AdminGamePassCreateSeasonForm()),
                default => $player->sendForm(new $this),
            };
        });
    }
}