<?php


namespace battleoase\battlecore\clanSystem\forms;


use battleoase\battlecore\clanSystem\api\Clan;
use battleoase\battlecore\clanSystem\api\ClanAPI;
use battleoase\battlecore\clanSystem\api\PlayerClanAPI;
use battleoase\battlecore\clanSystem\ClanSystem;
use battleoase\battlecore\pmmpExtensions\text;
use Frago9876543210\EasyForms\elements\Dropdown;
use Frago9876543210\EasyForms\elements\Input;
use Frago9876543210\EasyForms\forms\CustomForm;
use Frago9876543210\EasyForms\forms\CustomFormResponse;
use pocketmine\color\Color;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\utils\TextFormat as c;

class CreateClanForm extends CustomForm {


	public function __construct() {
		$colorArray = [];
		foreach (ClanSystem::COLORS as $colorName => $color) $colorArray[] = $color . $colorName;
		parent::__construct(
			ClanSystem::PREFIX,
			[
				new Input("§7Enter the Clan-§eName §7of your Clan (16 characters)", " "),
				new Input("§7Enter the Clan-§eTag §7of your Clan (6 characters)", " "),
				new Dropdown("§7Select a " . text::colorize("color") . "", $colorArray),
			], function(Player $player, CustomFormResponse $response): void{
				list($clan_name, $clan_tag, $color) = $response->getValues();

						if(preg_match("/^[a-zA-Z0-9]+$/", $clan_name) == 1) {
							if(preg_match("/^[a-zA-Z0-9]+$/", $clan_tag) == 1) {
								if (ClanSystem::count_words($clan_tag) <= 7) {
									if (ClanSystem::count_words($clan_name) <= 16) {
										if (!ClanAPI::isClan($clan_name)) {
											if (!ClanAPI::isClanTag($clan_tag)) {
												$c = TextFormat::clean($color);
												ClanAPI::createClan($player, $clan_name, $clan_tag, $c);
												PlayerClanAPI::setPlayersClan($player, $clan_name, ClanSystem::LEADER);
												$player->sendMessage(ClanSystem::PREFIX . c::GREEN . "You created a new clan!");
											} else {
												$player->sendMessage(ClanSystem::PREFIX . c::YELLOW . "Clantag is already in use!");
											}
										} else {
											$player->sendMessage(ClanSystem::PREFIX . c::YELLOW . "Clanname is already in use!");
										}
									}else{
										$player->sendMessage(ClanSystem::PREFIX . "§cThe max characters in an clan name is §e16!");
									}
								}else{
									$player->sendMessage(ClanSystem::PREFIX . "§cThe max characters in an clan tag is §e6!");
								}
							}else{
								$player->sendMessage(ClanSystem::PREFIX . "§cYou can only use letters from A - Z, a - z and numbers!");
							}
						}else{
							$player->sendMessage(ClanSystem::PREFIX . "§cYou can only use letters from A - Z, a - z and numbers!");
						}
			}
		);
	}

}