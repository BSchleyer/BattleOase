<?php

namespace battleoase\battlecore\gamePassSystem\form;

use battleoase\battlecore\BattleCore;
use battleoase\battlecore\gamePassSystem\GamePassSystem;
use battleoase\battlecore\pmmpExtensions\text;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;

class UnlockGamePassForm extends MenuForm {

    public function __construct() {

        parent::__construct(GamePassSystem::TITLE, "", [
            new MenuOption(text::colorize("Unlock")),
        ], function (Player $player, int $data): void{
            if ($data == 0){
                BattleCore::getInstance()->gamePassSystem->gamePassProvider->unlock($player, 1);
            }
        });
    }

}