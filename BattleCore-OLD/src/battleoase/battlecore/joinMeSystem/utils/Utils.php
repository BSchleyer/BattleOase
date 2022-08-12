<?php


namespace battleoase\battlecore\joinMeSystem\utils;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\joinMeSystem\JoinMeSystem;
use battleoase\battlecore\utils\Emoji;
use ceepkev77\cloudapi\CloudAPI;
use ceepkev77\cloudbridge\network\DataPacket;
use ceepkev77\cloudbridge\network\packet\ListCloudPlayersRequestPacket;
use ceepkev77\cloudbridge\network\packet\ListCloudPlayersResponsePacket;
use ceepkev77\cloudbridge\network\packet\PlayerMessagePacket;
use Frago9876543210\EasyForms\elements\Button;
use Frago9876543210\EasyForms\forms\MenuForm;
use pocketmine\network\mcpe\protocol\TransferPacket;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class Utils
{
	public static function createJoinME(string $player_name, string $server_name)
	{
		BattleCore::$connection->query("INSERT INTO Core.joinme_players(player_name, serverName) VALUES ('$player_name', '$server_name')");
	}

	public static function listAllJoinME(): array
	{
		$return = [];
		$result = BattleCore::$connection->query("SELECT * FROM Core.joinme_players");
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				$return[] = ['player_name' => $row['player_name'], 'server_name' => $row['serverName']] ;
			}
		}
		return $return;
	}

	/**
	 * @param string $player
	 * @return array
	 */

	public static function getJoinME(string $player): array
	{
		$return = [];
		$result = BattleCore::$connection->query("SELECT * FROM Core.joinme_players WHERE player_name='$player'");
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				$return[] = ['player_name' => $row['player_name'], 'server_name' => $row['serverName']];
			}
		}else{
			$return[] = false;
		}
		return $return;
	}

	public static function deleteJoinMEByServerName(string $server) : void
	{
		BattleCore::$connection->query("DELETE FROM Core.joinme_players WHERE `server_name`='$server'");
	}

	public static function deleteJoinMEByPlayerName(string $player) : void
	{
		BattleCore::$connection->query("DELETE FROM Core.joinme_players WHERE `player_name`='$player'");
	}

	public function onJoinMeListForm(Player $player){
		$options = [];
		foreach(Utils::listAllJoinME() as $name) {
			$options[] = new Button(TextFormat::GREEN . $name["player_name"] . "\n" . TextFormat::DARK_GRAY . $name["server_name"]);
			continue;
		}
		$options[] = new Button("§cBack");

		$player->sendForm(new MenuForm(
			"§4§lJoin§cME §r§f§7| §eList",
			"",
			$options,
			function (Player $player, Button $button): void {
				$str = explode("\n", $button->getText())[1];
				$buttonText = TextFormat::clean($str);
				if ($buttonText !== "Back"){
					$pk = new TransferPacket();
					$pk->address = (TextFormat::clean($str));
					$player->getNetworkSession()->sendDataPacket($pk);
				}
				$this->onJoinMeForm($player);
			}
		));

	}

	public function onJoinMeForm(Player $player){
		$player->sendForm(new MenuForm(
			"§4§lJoin§cME §r§f§7| §7Create", "",
			[
				new Button(BattleCore::getInstance()->getLanguageSystem()->translate($player, "joinMe.ui.createJoinMe")),
				new Button(BattleCore::getInstance()->getLanguageSystem()->translate($player, "joinMe.ui.listJoinMe")),
			],
			function (Player $player, Button $button): void{
				switch ($button->getValue()){
					case 0:
						if(JoinMeSystem::$player[$player->getName()] == false or $player->hasPermission("joinme")) {
							if(str_contains(Server::getInstance()->getMotd(), "Lobby")) {
								$player->sendMessage(BattleCore::getInstance()->getLanguageSystem()->translate($player,"joinME.is.not.Allowed"));
							} else {
								$server = Server::getInstance()->getMotd();
								$name = $player->getDisplayName();

								$requestPacket = new ListCloudPlayersRequestPacket();
								$requestPacket->submitRequest($requestPacket, function(DataPacket $packet) use ($server, $name) {
									if($packet instanceof ListCloudPlayersResponsePacket) {
										foreach ($packet->players as $playerName) {
											$v = "codee\n§4§lJoin§cME§r§f§8 | §r§f$name code7created a JoinME on code6codee$server coder\n§4§lJoin§cME§r§fcode8 | code7Join him with codeecodel/joinme\ncodee";;
											$message = str_replace("code", "§", $v);
											$pk = new PlayerMessagePacket();
											$pk->playerName = $playerName;
											$pk->value = $message;
											$pk->sendPacket();
										}
									}
								});

								Utils::createJoinME($player->getName(), $server);
								JoinMeSystem::$player[$player->getName()] = true;
								#JoinME::getInstance()->getScheduler()->scheduleDelayedTask(new DeleteJoinMETask($player), 20 * 60 * 5);
							}
						} else {
							$player->sendMessage(BattleCore::getInstance()->getLanguageSystem()->translate($player, "joinMe.exist")); //ToDo: add LanguageAPI
						}
						break;
					case 1:
						$this->onJoinMeListForm($player);
						break;
				}
			}
		));
	}
}

