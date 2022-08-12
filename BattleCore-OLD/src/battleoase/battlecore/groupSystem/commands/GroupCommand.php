<?php


namespace battleoase\battlecore\groupSystem\commands;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\groupSystem\api\GroupAPI;
use battleoase\battlecore\groupSystem\api\PlayerAPI;
use battleoase\battlecore\groupSystem\api\TimeAPI;
use battleoase\battlecore\groupSystem\GroupSystem;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\utils\TextFormat as c;

class GroupCommand extends Command
{
	public function __construct()
	{
		parent ::__construct("group", "Group Command", "/group <player <group>");
		$this->setPermission("admin");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if (isset($args[0])) {
			if($args[0] == "set") {
				if(isset($args[1]) && isset($args[2])) {
					if ($sender->hasPermission('admin')) {
						if (GroupSystem::getPlayerAPI() instanceof PlayerAPI) {
							if (GroupSystem::getPlayerAPI()->exitsGroup($args[2])) {
								$player = BattleCore::getInstance()->getServer()->getPlayerExact($args[1]);
								$group = GroupSystem::$groups[$args[2]];
								$name = $args[1];
								unset($args[0]);
								unset($args[1]);
								unset($args[2]);

								GroupSystem::getPlayerAPI()->setGroup($group, $name);

								if ($sender instanceof Player) {
									if ($player->isOnline()){
										$sender->sendMessage(BattleCore::getInstance()->getPrefix() . TextFormat::RED . BattleCore::getInstance()->getLanguageSystem()->translate($sender, "groupsystem.message.giveplayergroup", [
											'{NAME}' => $name,
											'{GROUP}' => $group->getColor() . $group->getName()
										]));

										$player->sendMessage(BattleCore::getInstance()->getPrefix() . TextFormat::RED . BattleCore::getInstance()->getLanguageSystem()->translate($sender, "groupsystem.message.setnewRank", [
												'{GROUP}' => $group->getColor() . $group->getName()
											]));

										GroupSystem::getPlayerAPI()->setPermissions($player);
										GroupSystem::getPlayerAPI()->setPrefix($player);

									}else{
										foreach(Server::getInstance()->getOnlinePlayers() as $player) {
											GroupSystem::getPlayerAPI()->setPermissions($player);
											GroupSystem::getPlayerAPI()->setPrefix($player);
										}

										$sender->sendMessage(BattleCore::getInstance()->getPrefix() . TextFormat::RED . BattleCore::getInstance()->getLanguageSystem()->translate($sender, "groupsystem.message.giveplayergroup", [
											'{NAME}' => $name,
											'{GROUP}' => $group->getColor() . $group->getName()
										]));
									}
								} else {
									$sender->sendMessage(BattleCore::getInstance()->getLanguageSystem()->translate($sender, "groupsystem.message.giveplayergroup", [
										'{NAME}' => $name,
										'{GROUP}' => $group->getColor() . $group->getName()
									]));
								}

							} else {
								$sender->sendMessage(BattleCore::getInstance()->getPrefix() . TextFormat::RED . BattleCore::getInstance()->getLanguageSystem()->translate($sender, "groupsystem.message.groupNotfound"));
							}
						}
					}
				} else {
					$sender->sendMessage(GroupSystem::PREFIX . c::RED . '/group set <player> <group>');
				}
			} elseif($args[0] == "info") {
				$sender->sendMessage(GroupSystem::PREFIX . "§7Your Rank expired in: " . "§cPERMANTLY");
			}
		} else {
			$sender->sendMessage(GroupSystem::PREFIX . c::RED . '/group <set | info>');
		}
	}
}