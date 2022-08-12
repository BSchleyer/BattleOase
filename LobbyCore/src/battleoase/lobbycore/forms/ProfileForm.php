<?php


namespace battleoase\lobbycore\forms;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\messageSystem\MessageSystem;
use Frago9876543210\EasyForms\elements\Button;
use Frago9876543210\EasyForms\elements\Image;
use Frago9876543210\EasyForms\forms\MenuForm;
use pocketmine\player\Player;
use pocketmine\Server;

class ProfileForm
{
	public Player $player;


	public function __construct(Player $player)
	{
		$this->player = $player;
	}

	public function open(){
		$player = $this->player;

		$player->sendForm(new MenuForm(
			"§8• §3Profile §8• ", "",
			[
				new Button("§a" . BattleCore::translate($player, "lobby.profile.yourFriends")),
				new Button("§c" . BattleCore::translate($player, "lobby.profile.settings")),
				new Button("§d" . BattleCore::translate($player, "lobby.profile.language")),
				new Button("§e" . BattleCore::translate($player, "lobby.profile.yourClan")),
				new Button("§5" . BattleCore::translate($player, "lobby.profile.party")),
				new Button("§e" . BattleCore::translate($player, "lobby.profile.stats")),
			],
			function(Player $player, Button $selected) : void{
				switch ($selected->getValue()){
					case 0:
						Server::getInstance()->dispatchCommand($player, "friends");
						break;
					case 1:
						$settingsform = new SettingsForm($player);
						$settingsform->open();
						break;
					case 2:
						Server::getInstance()->dispatchCommand($player, "clan");
						break;
					case 3:
						Server::getInstance()->dispatchCommand($player, "party");
						break;
					case 4:
						Server::getInstance()->dispatchCommand($player, "stats");
						break;
				}
			}
		));
	}
}