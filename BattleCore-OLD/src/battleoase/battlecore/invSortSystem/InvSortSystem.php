<?php


namespace battleoase\battlecore\invSortSystem;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\utils\BPlugin;
use ceepkev77\cloudbridge\CloudBridge;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class InvSortSystem extends BPlugin
{
	public function __construct()
	{
		BattleCore::getInstance()->getMysqlConnection()->query("CREATE TABLE IF NOT EXISTS Core.inventories(player_name VARCHAR(255), inventory TEXT, armor TEXT, game TEXT)");
	}

	public static function giveInventory(Player $player, $game)
	{
		$name = $player->getName();
		$inv = self::getInventory($name, $game);
		$armor = self::getArmorInventory($name, $game);

		$player->getInventory()->clearAll();
		$player->getArmorInventory()->clearAll();

		foreach ($inv as $slot => $item) {
			if($item instanceof Item) {
				if ($item->getCustomName() == "§aSave!") {
				} else if($item->getId() === ItemFactory::getInstance()->get(ItemIds::SANDSTONE)) {
					$item->setCount(64);
					$player->getInventory()->setItem($slot, $item);
				} else {
					$player->getInventory()->setItem($slot, $item);
				}
			}

		}

		if(!CloudBridge::getGameServer()->getCloudGroup()->getName() == "SpeedCW-2x1" or !CloudBridge::getGameServer()->getCloudGroup()->getName() == "SpeedCW-2x2" ) {
			$player->getInventory()->setItem(17, ItemFactory::getInstance()->get(ItemIds::SLIME_BALL)->setCustomName("§aSave!"));
		}

		if(isset($armor["helmet"])) $player->getArmorInventory()->setHelmet($armor["helmet"]);
		if(isset($armor["chestplate"])) $player->getArmorInventory()->setChestplate($armor["chestplate"]);
		if(isset($armor["leggings"])) $player->getArmorInventory()->setLeggings($armor["leggings"]);
		if(isset($armor["boots"])) $player->getArmorInventory()->setBoots($armor["boots"]);
	}

	public static function saveInventory(Player $player, $game)
	{
		$name = $player->getName();
		$invContent = $player->getInventory()->getContents();
		$armorContent = $player->getArmorInventory()->getContents();

		$inv64 = [];
		$armor64 = [];

		foreach ($invContent as $slot => $item) {
			if(!$item->getCustomName() == "§aSave!") {
				$inv64[$slot] = $item; // Maybe useless... its useless lmao
			}

		}
		foreach ($armorContent as $slot => $item) {
			switch ($slot) {
				case 0:
					$armor64["helmet"] = $item;
					break;
				case 1:
					$armor64["chestplate"] = $item;
					break;
				case 2:
					$armor64["leggings"] = $item;
					break;
				case 3:
					$armor64["boots"] = $item;
					break;
			}
		}

		$inv64 = base64_encode(serialize($inv64));
		$armor64 = base64_encode(serialize($armor64));

		BattleCore::getInstance()->getMysqlConnection()->query("UPDATE Core.inventories SET inventory='$inv64', armor='$armor64' WHERE player_name='$name' AND game='$game'");
	}

	public static function getInventory(string $name, $game)
	{
		$res = BattleCore::getInstance()->getMysqlConnection()->query("SELECT * FROM Core.inventories WHERE player_name='$name' AND game='$game'");

		$array = $res->fetch_array();

		$str = base64_decode($array["inventory"]);
		return unserialize($str);
	}

	public static function getArmorInventory(string $name, $game)
	{
		$res = BattleCore::getInstance()->getMysqlConnection()->query("SELECT * FROM Core.inventories WHERE player_name='$name' AND game='$game'");

		$array = $res->fetch_array();

		$str = base64_decode($array["armor"]);
		$armorInv = unserialize($str);

		return $armorInv;
	}

	public static function register(Player $player, $game)
	{
		$name = $player->getName();
		$invContent = $player->getInventory()->getContents();
		$armorContent = $player->getArmorInventory()->getContents();

		$inv64 = [];
		$armor64 = [];

		foreach ($invContent as $slot => $item) {
			$inv64[$slot] = $item;
		}
		foreach ($armorContent as $slot => $item) {
			$armor64[$slot] = $item;
		}

		$inv64 = base64_encode(serialize($inv64));
		$armor64 = base64_encode(serialize($armor64));

		BattleCore::getInstance()->getMysqlConnection()->query("INSERT INTO Core.inventories(player_name, inventory, armor, game) VALUES ('$name', '$inv64', '$armor64', '$game')");
	}

	public static function isRegistered(Player $player, $game)
	{
		$name = $player->getName();

		$res = BattleCore::getInstance()->getMysqlConnection()->query("SELECT * FROM Core.inventories WHERE player_name='$name' AND game='$game'");

		if($res->num_rows <= 0){
			return false;
		} else {
			return true;
		}
	}
}