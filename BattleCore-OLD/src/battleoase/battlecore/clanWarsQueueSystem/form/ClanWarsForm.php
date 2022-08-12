<?php

namespace battleoase\battlecore\clanWarsQueueSystem\form;

use battleoase\battlecore\clanSystem\api\PlayerClanAPI;
use battleoase\battlecore\clanWarsQueueSystem\ClanWarsQueueSystem;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use Frago9876543210\EasyForms\elements\Dropdown;
use Frago9876543210\EasyForms\forms\CustomForm;
use Frago9876543210\EasyForms\forms\CustomFormResponse;
use pocketmine\player\Player;
use pocketmine\Server;

class ClanWarsForm extends MenuForm {

    public function __construct() {

    	parent::__construct("§a§lClanWars", "", [
    		new MenuOption("§7ClanWars"),
			new MenuOption("§7Running Games"),
			], function (Player $player, int $data): void{
    		match ($data) {
    			0 => ClanWarsQueueSystem::sendQueue($player, 2),
				1 => Server::getInstance()->dispatchCommand($player, "soon"),
				default => $player->sendForm(new $this),
			};
    	});
    }
}