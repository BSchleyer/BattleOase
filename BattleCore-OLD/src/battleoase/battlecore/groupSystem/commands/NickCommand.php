<?php


namespace battleoase\battlecore\groupSystem\commands;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\groupSystem\api\PlayerAPI;
use battleoase\battlecore\groupSystem\GroupSystem;
use pocketmine\block\inventory\FurnaceInventory;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Skin;
use pocketmine\lang\Translatable;
use pocketmine\network\mcpe\convert\SkinAdapterSingleton;
use pocketmine\network\mcpe\protocol\PlayerListPacket;
use pocketmine\network\mcpe\protocol\types\PlayerListEntry;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;

class NickCommand extends Command
{

	public function __construct()
	{
		parent::__construct("nick", "Nick Command", "/nick");
		$this->setPermission("premium");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if($sender->hasPermission("premium")) {
			if(!isset($args[0])) {
				if ($sender instanceof Player) {
					$nickdata = GroupSystem::getPlayerAPI()->getPlayerInfo($sender)['Nick'];
					if ($nickdata != "NULL") {
						$name = $sender->getName();
						BattleCore::getInstance()->getMysqlConnection()->query("UPDATE Core.group_players SET `nick`='NULL',`skin_player_name`='NULL' WHERE `player_name`='$name'");
						BattleCore::getInstance()->getMysqlConnection()->query("UPDATE Core.group_nicknames SET `used`='false' WHERE `name`='$nickdata'");
						$sender->setSkin(GroupSystem::$Skins[$sender->getName()]);
						GroupSystem::getPlayerAPI()->setPrefix($sender);
						$sender->sendMessage(BattleCore::getInstance()->getLanguageSystem()->translate($sender, "groupSystem.message.unnick"));

						$result = BattleCore::getInstance()->getMysqlConnection()->query("SELECT * FROM Stats.Skins WHERE player_name='$sender'");
						if ($result->num_rows > 0) {
							while ($row = $result->fetch_assoc()) {
								$sender->setSkin(new Skin(uniqid(), zlib_decode(base64_decode($row["skin_data"])), $row["cape_data"], $row["geometry_name"], $row["geometry_data"]));
							}
						}

					} else {

						$sender->getNetworkSession()->sendDataPacket(PlayerListPacket::remove([PlayerListEntry::createRemovalEntry($sender->getUniqueId())]));
						$sender->getNetworkSession()->sendDataPacket(PlayerListPacket::add([PlayerListEntry::createAdditionEntry($sender->getUniqueId(), $sender->getId(), $sender->getDisplayName(), SkinAdapterSingleton::get()->toSkinData($sender->getSkin()), $sender->getXuid())]));

						$name = $sender->getName();
						$result = BattleCore::getInstance()->getMysqlConnection()->query("SELECT * FROM Stats.Skins WHERE LENGTH(player_name) <= 16 and player_name != 'BATTLEOASE' and player_name != '{$name}' ORDER BY RAND() LIMIT 1");
						if ($result->num_rows > 0) {
							while ($row = $result->fetch_assoc()) {
								$sender->setSkin(new Skin(uniqid(), zlib_decode(base64_decode($row["skin_data"])), $row["cape_data"], $row["geometry_name"], $row["geometry_data"]));
								$skinname = $row["player_name"];
							}
						}

						$result = BattleCore::getInstance()->getMysqlConnection()->query("SELECT * FROM Core.group_nicknames WHERE used='false' ORDER BY RAND() LIMIT 1");
						if ($result->num_rows > 0) {
							while ($row = $result->fetch_assoc()) {
								if (!$row["name"] == null) {
									$nick = $row["name"];
									BattleCore::getInstance()->getMysqlConnection()->query("INSERT INTO Core.group_players (`player_name`, `group_name`, `nick`, `skin_player_name`, `color`) VALUES ('$name', 'Player', '$nick',  '$skinname', '§7');");
									BattleCore::getInstance()->getMysqlConnection()->query("UPDATE Core.group_players SET `nick`='$nick',`skin_player_name`='$skinname' WHERE `player_name`='$name'");
									BattleCore::getInstance()->getMysqlConnection()->query("UPDATE Core.group_nicknames SET `used`='true' WHERE `name`='$nick'");

									$log = new Config("/home/cloud/data/groupsystem/nick.yml", Config::YAML);
									$now = new \DateTime('now', new \DateTimeZone("Europe/Berlin"));

									$test = Server::getInstance()->getMotd();
									$log->set("[" . $now->format("m-d-H-i-s") . "] [$test]", $sender->getName() . " -> " . $row["name"]);
									$log->save();

									GroupSystem::getPlayerAPI()->setPrefix($sender);
									$sender->sendMessage(BattleCore::getInstance()->getLanguageSystem()->translate($sender, "groupSystem.message.nick", [
										"{NAME}" => $row["name"]
									]));
								}
							}
						}
					}
				}
			} else {
				if($sender->hasPermission("admin")) {
					BattleCore::getInstance()->getMysqlConnection()->query("INSERT INTO Core.group_nicknames(`name`, `used`) VALUES ('$args[0]', 'false')");
					$sender->sendMessage(BattleCore::getInstance()->getLanguageSystem()->translate($sender, "groupSystem.message.addnick"));
				}
			}
		} else {
			if ($sender instanceof Player) {
				$sender->sendMessage(BattleCore::getInstance()->getLanguageSystem()->translate($sender, "noPerms"));
			}
		}

	}

}