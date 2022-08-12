<?php


namespace battleoase\lobbycore\forms;


use battleoase\lobbycore\forms\feature\BootsFeatureForm;
use battleoase\lobbycore\LobbyCore;
use Frago9876543210\EasyForms\elements\Button;
use Frago9876543210\EasyForms\forms\MenuForm;
use pocketmine\player\Player;

class FeatureForm
{
	public Player $player;


	public function __construct(Player $player)
	{
		$this->player = $player;
	}

	public function open(){
		$this->player->sendForm(new MenuForm(
			"§8• §6Feature §8• ", "",
			[
				new Button("§dBoots")
			],
			function (Player $player, Button $selected): void {
				switch ($selected->getValue()){
					case 0:
						/*$bff = new BootsFeatureForm($player);
						$bff->open();*/
						$player->sendMessage(LobbyCore::PREFIX . "§cNo access!");
						break;
				}
			}
		));
	}
}