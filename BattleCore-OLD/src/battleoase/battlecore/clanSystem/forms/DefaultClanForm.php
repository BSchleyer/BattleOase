<?php


namespace battleoase\battlecore\clanSystem\forms;


use battleoase\battlecore\clanSystem\ClanSystem;
use Frago9876543210\EasyForms\elements\Button;
use Frago9876543210\EasyForms\forms\MenuForm;
use pocketmine\player\Player;
use pocketmine\Server;

class DefaultClanForm extends MenuForm {

	public function __construct() {
		parent::__construct(
			ClanSystem::PREFIX,
			" ",
			[
				new Button("§3Create Clan"),
				new Button("§bInvites"),
				new Button("§6Top 10")
			], function(Player $player, Button $button): void {
				switch ($button->getValue()) {
					case 0:
						$player->sendForm(new CreateClanForm());
						break;
					case 1:
						//stuff
						break;
					case 2:
						//stuff d
						break;
				}
			}
		);
	}

}