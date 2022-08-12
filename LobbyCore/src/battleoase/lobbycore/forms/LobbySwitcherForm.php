<?php


namespace battleoase\lobbycore\forms;


use battleoase\battlecore\BattleCore;
use battleoase\lobbycore\LobbyCore;
use ceepkev77\cloudbridge\CloudBridge;
use ceepkev77\cloudbridge\network\DataPacket;
use ceepkev77\cloudbridge\network\packet\PlayerMovePacket;
use ceepkev77\cloudbridge\objects\GameServer;
use ceepkev77\lobbyapi\LobbyAPI;
use Frago9876543210\EasyForms\elements\Button;
use pocketmine\network\mcpe\protocol\TransferPacket;
use pocketmine\player\Player;
use Frago9876543210\EasyForms\forms\MenuForm;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class LobbySwitcherForm
{
	public Player $player;


	public function __construct(Player $player)
	{
		$this->player = $player;
	}

	public function open(){
		$buttons = [];
		foreach (LobbyAPI::getGameServerProvider()->getGameServers() as $server){
			if ($server instanceof GameServer){
				if ($server->getCloudGroup()->getName() === "Lobby"){
					if (Server::getInstance()->getMotd() == $server->getName()){
						$buttons[] = ("§8• §c§l".$server->getName() . " §e(YOUR LOBBY)" . "§r§f".PHP_EOL . "§8| §e" . $server->getPlayerCount() . "§8/§e" . Server::getInstance()->getMaxPlayers());
					}else{
						$buttons[] = ("§8• §b§l".$server->getName() . "§r§f".PHP_EOL . "§8| §e" . $server->getPlayerCount() . "§8/§e" . Server::getInstance()->getMaxPlayers());
					}
					asort($buttons);
				}
			}
		}
		$this->player->sendForm(new MenuForm(
			"§8• §bLobby-Switcher §r§8•", "§7There are §e" . count($buttons) . " §7Lobbies §7available!", $buttons,
			function (Player $player, Button $selected): void {
				$str = explode("\n", $selected->getText())[0];
				$cls = str_replace(["•", " "], ["", ""], $str);
                $test = str_replace("(YOURLOBBY)", "", TextFormat::clean($cls));
                if($test !== CloudBridge::getGameServer()->getName()) {
                    $pk = new PlayerMovePacket();
                    $pk->toServer = TextFormat::clean($test);
                    $pk->playerName = $player->getName();
                    $pk->sendPacket();
                } else {
                    $player->sendMessage("§bCloud §7| §cYou already on the Server");
                }

			}
		));
	}
}