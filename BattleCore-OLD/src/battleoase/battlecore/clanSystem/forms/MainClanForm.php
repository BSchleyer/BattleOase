<?php


namespace battleoase\battlecore\clanSystem\forms;


use battleoase\battlecore\clanSystem\api\PlayerClanAPI;
use battleoase\battlecore\clanSystem\ClanSystem;
use Frago9876543210\EasyForms\elements\Button;
use Frago9876543210\EasyForms\elements\Image;
use Frago9876543210\EasyForms\forms\MenuForm;
use pocketmine\player\Player;
use pocketmine\Server;
use Reward\Main;

class MainClanForm extends MenuForm {

	public function __construct(Player $player) {
		$buttons = [];

		$buttons[] = new Button("§eClanWar", new Image("https://cdn.iconscout.com/icon/premium/png-64-thumb/war-location-army-weapons-battle-soldier-military-43852.png"));
		$buttons[] = new Button("§7Clan-Info", new Image("https://cdn.iconscout.com/icon/free/png-64/information-123-404698.png"));
		$buttons[] = new Button("§3Members", new Image("https://cdn.iconscout.com/icon/premium/png-64-thumb/members-8-374014.png"));

		if (PlayerClanAPI::getPlayersClanData($player)->getRank() == ClanSystem::LEADER){
			$buttons[] = new Button("§cDelete Clan", new Image("https://cdn.iconscout.com/icon/premium/png-64-thumb/delete-52-103683.png"));
		}

		parent::__construct(
			ClanSystem::PREFIX,
			" ",
			$buttons, function(Player $player, Button $button): void {
				$clan = PlayerClanAPI::getPlayersClanData($player)->getClanName();
				switch ($button->getValue()) {
					case 0:
						$player->sendMessage(ClanSystem::PREFIX . "Soon...");
						break;
					case 1:
						$player->sendForm(new ClanInfoForm($clan));
						break;
					case 2:
						$player->sendForm(new MemberListForm($clan));
						break;
					case 3:
						$player->sendForm(new ConfirmDeleteClanForm($player));
						break;
				}
			}
		);
	}

}