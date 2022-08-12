<?php


namespace battleoase\battlecore\clanSystem\forms;

use battleoase\battlecore\clanSystem\api\PlayerClanAPI;
use battleoase\battlecore\clanSystem\ClanSystem;
use Closure;
use Frago9876543210\EasyForms\elements\Button;
use Frago9876543210\EasyForms\elements\Image;
use Frago9876543210\EasyForms\forms\MenuForm;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as c;

class MemberListForm extends MenuForm {

	public function __construct($clan) {
		$playerNames = [];
		foreach (PlayerClanAPI::getPlayersInClan($clan) as $p_name) {
			$rankInt = PlayerClanAPI::getPlayersClanData($p_name)->getRank();
			$rankFullName = ClanSystem::rankIntToString($rankInt);
			$rankColor = ClanSystem::rankStringToColor($rankFullName);

			$playerNames[] = new Button($rankColor . $p_name, new Image("http://battleoase.net/api/battleoase/players/$p_name/head/$p_name.png"));
		}

		parent::__construct(
			ClanSystem::PREFIX . " §3Members",
			"",
			$playerNames,
			function(Player $player, Button $button): void{
				//stuff...
			}
		);
	}

}