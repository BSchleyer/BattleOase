<?php


namespace battleoase\lobbycore\forms;


use battleoase\battlecore\BattleCore;
use Frago9876543210\EasyForms\elements\Button;
use Frago9876543210\EasyForms\forms\MenuForm;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;

class LobbyGamesForm
{
	public Player $player;


	public function __construct(Player $player)
	{
		$this->player = $player;
	}

	public function open(){
		$player = $this->player;
		$games = new Games($player);

		$player->sendForm(new MenuForm(
			"§e§lLobby§7§r-§e§lGames", "",
			[
				new Button("§9§lBridge\n§r§f§cNEW!"),
				new Button("§e§lJumpAndRun"),
				new Button("§7???")
			],
			function(Player $player, Button $selected) : void{
				switch ($selected->getValue()){
					case 0:
						$player->teleport(new Vector3(-42721, 85, -5962));
						$player->sendMessage(BattleCore::getPrefix() . BattleCore::getInstance()->getLanguageSystem()->translate($player, "lobbygames.teleport.bridge"));
						break;
					case 1:
						$player->teleport(new Vector3(-42693, 46, -5885));
						$player->sendMessage(BattleCore::getPrefix() . BattleCore::getInstance()->getLanguageSystem()->translate($player, "lobbygames.teleport.jumpAndRun"));
						break;
					case 2:
						break;
				}
			}
		));
	}
}