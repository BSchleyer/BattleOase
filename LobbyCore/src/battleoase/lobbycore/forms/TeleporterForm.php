<?php


namespace battleoase\lobbycore\forms;


use battleoase\lobbycore\LobbyCore;
use ceepkev77\cloudbridge\network\DataPacket;
use ceepkev77\cloudbridge\network\packet\ListServerRequestPacket;
use ceepkev77\cloudbridge\network\packet\ListServerResponsePacket;
use ceepkev77\cloudbridge\network\packet\PlayerMovePacket;
use Frago9876543210\EasyForms\elements\Button;
use Frago9876543210\EasyForms\elements\Image;
use Frago9876543210\EasyForms\forms\MenuForm;
use pocketmine\block\StoneButton;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\TransferPacket;
use pocketmine\player\Player;

class TeleporterForm
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
			"§8• §eTeleporter §8•", "",
			[
				new Button("§r§fSpawn"),
				new Button("§1Cores"),
				new Button("§dMLGRush",),
				new Button("§cBed§r§fWars"),
				new Button("§6FF§cA§7-§r§fGames"),
				new Button("§l§1Replay§r§f§lServer"),
			],
			function(Player $player, Button $selected) use ($games) : void{
				$text = $selected->getText();
				switch ($selected->getValue()){
					case 0:
						$games::teleportToGame(new Vector3(-42752, 49, -5888), $text);
						break;
					case 1:
						$games::teleportToGame(new Vector3(-42876, 48, -5920), $text);
						break;
					case 2:
						$games::teleportToGame(new Vector3(-42884, 46, -6017), $text);
						break;
					case 3:
						$games::teleportToGame(new Vector3(-42625, 48, -5923), $text);
						break;
					case 4:
						$games::teleportToGame(new Vector3(-42666, 46, -6054), $text);
						break;
					case 5:
						$pk = new ListServerRequestPacket();
						$pk->submitRequest($pk, function(DataPacket $dataPacket) use ($player) {
							if ($dataPacket instanceof ListServerResponsePacket){
								$servers = json_decode($dataPacket->data["servers"], true);
								$freeServer = [];
								foreach ($servers as $server){
									$text = explode("-", $server);
									if ($text[0] === "ReplayServer"){
										$freeServer[] = $text;
									}
								}
								if (count($freeServer) == 0){
									$player->sendMessage(LobbyCore::PREFIX . "§cKein freier Server gefunden!");
								}else{
									$pk = new PlayerMovePacket();
									$pk->toServer = array_rand($freeServer);
									$pk->playerName = $player->getName();
									$pk->sendPacket();
								}
							}
						});
				}
			}
		));
	}
}