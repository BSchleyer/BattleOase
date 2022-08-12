<?php


namespace battleoase\battlecore\groupSystem\api;


use battleoase\battlecore\BattleCore;

use battleoase\battlecore\BattlePlayer;
use battleoase\battlecore\clanSystem\api\ClanAPI;
use battleoase\battlecore\clanSystem\api\PlayerClanAPI;
use battleoase\battlecore\clanSystem\ClanSystem;
use PHPMailer\Test\PHPMailer\ValidateAddressCustomValidatorTest;
use pocketmine\network\mcpe\convert\SkinAdapterSingleton;
use pocketmine\network\mcpe\protocol\PlayerListPacket;
use pocketmine\network\mcpe\protocol\types\PlayerListEntry;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\PermissionManager;
use battleoase\battlecore\groupSystem\GroupSystem;
use battleoase\battlecore\groupSystem\objects\Group;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as c;

class PlayerAPI
{

	public function __construct() {}

	public function playerExist($player): bool
	{
		if ($player instanceof Player){
			$name = $player->getName();
		}else{
			$name = $player;
		}
		return BattleCore::getInstance()->getMysqlConnection()->query("SELECT * FROM Core.group_players WHERE player_name='$name'")->num_rows === 1;
	}

	public function setGroup(Group $group, string $player){
		if ($player instanceof Player){
			$name = $player->getName();
		}else{
			$name = $player;
		}

		$group = $group->getName();

		BattleCore::getInstance()->getMysqlConnection()->query("UPDATE Core.group_players SET group_name='$group' WHERE player_name='$name'");
	}

	public function setDefaultGroup($player){
		if ($player instanceof Player){
			$name = $player->getName();
		}else{
			$name = $player;
		}

		$group = GroupSystem::DEFAULT_GROUP;

		BattleCore::getInstance()->getMysqlConnection()->query("UPDATE Core.group_players SET group_name='$group' WHERE player_name='$name'");
	}

	public function removeGroup($player){
		if ($player instanceof Player){
			$name = $player->getName();
		}else{
			$name = $player;
		}

		$default_group = GroupSystem::DEFAULT_GROUP;

		BattleCore::getInstance()->getMysqlConnection()->query("UPDATE Core.group_players SET group_name='$default_group' WHERE player_name='$name'");
	}

	public function getGroup($player) {
		if($player instanceof Player) {
			$player = $player->getName();
		}

		$query = BattleCore::getInstance()->getMysqlConnection()->query("SELECT * FROM Core.group_players WHERE player_name='$player'");
		if ($query->num_rows > 0){
			while($result = $query->fetch_assoc()){
				return $result["group_name"];
			}
		}
		return false;
	}

	public function importColorGroups(){
		foreach (GroupSystem::$groups as $group){
			$name = $group->getName();
			$color = $group->getColor();
			BattleCore::getInstance()->getMysqlConnection()->query("UPDATE Core.group_players SET color='".$color."' WHERE group_name = '$name' ");
		}
	}

	public function getPlayerInfo($player) : array
	{
		GroupSystem::getGroupConfig()->reload();

		if ($player instanceof Player){
			$name = $player->getName();
		}else{
			$name = $player;
		}

		$result = BattleCore::getInstance()->getMysqlConnection()->query("SELECT * FROM Core.group_players WHERE player_name = '$name'");

		if($result->num_rows == 0) {
			$group = GroupSystem::DEFAULT_GROUP;
			$nick = null;
		} else {
			if ($result->num_rows > 0) {
				while ($row = $result->fetch_assoc()) {
					$group = $row["group_name"];
					$nick = $row["nick"];
				}
			}
		}

		return $pinfo = [
			'Name' => $player,
			'Group' => $group,
			'Nick' => $nick
		];
	}

	public function getChat(Player $player, $msg) {
		$info = $this->getPlayerInfo($player);
		$filechatformat = GroupSystem::$groups[$info["Group"]]->getChatFormat();

		if($info['Nick'] != "NULL") {
			$chatformat = str_replace("&", c::ESCAPE, str_replace("%group%", GroupSystem::DEFAULT_GROUP, str_replace("%name%", $info['Nick'], str_replace("%msg%", $msg, str_replace('{LINE}', "\n",$filechatformat)))));
		} else {
			$chatformat = str_replace("&", c::ESCAPE, str_replace("%group%", $info['Group'], str_replace("%name%", $player->getName(), str_replace("%msg%", $msg, str_replace('{LINE}', "\n",$filechatformat)))));
		}

		if ($player instanceof BattlePlayer){
			if ($player->isInClan()){
				$data = PlayerClanAPI::getPlayersClanData($player);
				$chatformat = str_replace('%clan% ', (PlayerClanAPI::isInClan($player->getName()) ? "§7[" . ClanSystem::COLORS[ClanAPI::getClan($data->getClanName())->getColor()] . ClanAPI::getClan($data->getClanName())->getClanTag() ."§7] " : ''), $chatformat);
			} else {
				$chatformat = str_replace('%clan% ', '', $chatformat);
			}
		}

		return $chatformat;
	}

	public function exitsGroup($group) : bool
	{
		if (GroupSystem::getGroupConfig()->exists($group)) {
			return true;
		} else {
			return false;
		}
	}

	public function getGroupsPermissions($group)
	{
	    $permissions = [];
		if ($this->exitsGroup($group)) {
			$permissions = GroupSystem::$groups[$group]->getPermissions();
			return $permissions;
		}
		return $permissions;
	}

	public function getGroupsInheritancePermissions($groups)
	{
		$permissions = [];
		foreach ($groups as $group) {
			$permissions = array_merge($permissions, $this->getGroupsPermissions($group));
		}
		return $permissions;
	}

	public function getGroupInheritance($group) {
		if ($this->exitsGroup($group)) {
			$groupinfo = $this->getGroup($group);
			return [];
		}
		return [];
	}

	public function unsetPermissions(Player $player)
	{
		$player->addAttachment(BattleCore::getInstance())->clearPermissions();
	}

	public function setPermissions(Player $player)
	{

		$info = $this->getPlayerInfo($player);
		$this->unsetPermissions($player);
		foreach (array_merge($this->getGroupsPermissions($info['Group']), $this->getGroupsInheritancePermissions($this->getGroupInheritance($info['Group']))) as $value) {
		    if($value === "*") {
                foreach(PermissionManager::getInstance()->getPermissions() as $permission) {
                    $player->addAttachment(BattleCore::getInstance())->setPermission($permission->getName(), true);
                }
            } else {
                $player->addAttachment(BattleCore::getInstance())->setPermission($value, true);
            }
		}
	}


	public function setPrefix(Player $player)
	{


		$groupapi = new GroupAPI();
		$info = $this->getPlayerInfo($player);

		if ($info['Nick'] != "NULL") {
			$filenicknametag = $groupapi->getNameTag(GroupSystem::DEFAULT_GROUP);
			$nametag = str_replace('%name%', $player->getName(), str_replace('&', c::ESCAPE, str_replace('%group%', GroupSystem::DEFAULT_GROUP, $filenicknametag)));
			$player->setNameTag($nametag);
		} else {

			$filenametag = $groupapi->getNameTag($info['Group']);
			$nametag = str_replace('%name%', $player->getName(), str_replace('&', c::ESCAPE, str_replace('%group%', $info['Group'], $filenametag)));

			$player->setNameTag($nametag);
			$player->setDisplayName($nametag);

			$player->getNetworkSession()->sendDataPacket(PlayerListPacket::remove([PlayerListEntry::createRemovalEntry($player->getUniqueId())]));
			$player->getNetworkSession()->sendDataPacket(PlayerListPacket::add([PlayerListEntry::createAdditionEntry($player->getUniqueId(), $player->getId(), $player->getDisplayName(), SkinAdapterSingleton::get()->toSkinData($player->getSkin()), $player->getXuid())]));
		}
	}
}