<?php


namespace battleoase\lobbycore\forms;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\groupSystem\api\GroupAPI;
use battleoase\battlecore\groupSystem\api\PlayerAPI;
use battleoase\battlecore\groupSystem\GroupSystem;
use battleoase\battlecore\languageSystem\objects\Language;
use battleoase\lobbycore\LobbyCore;
use battleoase\lobbycore\utils\SettingUtils;
use battleoase\lobbycore\utils\Utils;
use Frago9876543210\EasyForms\elements\Button;
use Frago9876543210\EasyForms\elements\Dropdown;
use Frago9876543210\EasyForms\elements\Image;
use Frago9876543210\EasyForms\elements\Toggle;
use Frago9876543210\EasyForms\forms\CustomForm;
use Frago9876543210\EasyForms\forms\CustomFormResponse;
use Frago9876543210\EasyForms\forms\MenuForm;
use pocketmine\player\Player;
use pocketmine\Server;

class SettingsForm
{
	public Player $player;

	private array $buttonsLocal;

	public function __construct(Player $player)
	{
		$this->player = $player;
	}

	public function open(){
		$player = $this->player;

		foreach (BattleCore::getInstance()->getLanguageSystem()->languages as $language) {
			if($language instanceof Language) {
				$this->buttonsLocal[] = $language->getLocale();
			}
		}

		$player->sendForm(new CustomForm(
			"§8• §cSettings §8•",
			[
				new Toggle("§eHotbar-Geräusche", SettingUtils::get($player->getName())["hotbarSounds"]),
				new Toggle("§eDoppelsprung", SettingUtils::get($player->getName())["doubleJump"]),
				new Dropdown("§eSprache", $this->buttonsLocal),
			],
			function(Player $player, CustomFormResponse $response) : void{
				list($hotbar, $doppelsprung, $language) = $response->getValues();
				$player->setInfo("lang", $language);
				$player->sendMessage(LobbyCore::PREFIX . "§aThe changes are now saved!");

				SettingUtils::update($player->getName(), [
					"hotbarSounds" => $hotbar,
					"doubleJump" => $doppelsprung
				]);

			}
		));
	}
}