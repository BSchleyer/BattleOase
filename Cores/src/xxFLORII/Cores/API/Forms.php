<?php

namespace xxFLORII\Cores\API;

use Frago9876543210\EasyForms\elements\Button;
use Frago9876543210\EasyForms\forms\MenuForm;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use xxFLORII\Cores\Main;

class Forms {

	/**
	 * Function sendTeamUI
	 * @param Player $player
	 * @return void
	 */
	public static function sendTeamUI(Player $player): void
	{
		$countRed = count(Main::$redTeam);
		$countBlue = count(Main::$blueTeam);
		$buttons = ["§4Red\n§c{$countRed} §ePlayer(s)", "§1Blue\n§3{$countBlue} §ePlayer(s)"];
		$player->sendForm(new MenuForm(
			"§aFriends",
			"",
			$buttons,
			function (Player $player, Button $button): void {
				$cfg = Main::getInstance()->getConfig();
				if ($cfg->get("ingame") === false) {
					$str = explode("\n", $button->getText())[0];
					if (TextFormat::clean($str) === "Red") (new CoresAPI())->joinTeam($player, "red");
					if (TextFormat::clean($str) === "Blue") (new CoresAPI())->joinTeam($player, "blue");
				} else {
					$player->sendMessage(Main::getPrefix() . "§cThis game is already running.");
				}
			}
		));
	}

}