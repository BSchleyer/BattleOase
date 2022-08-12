<?php


namespace battleoase\bedwars;

use battleoase\bedwars\caches\MapCache;
use battleoase\bedwars\classes\Map;
use battleoase\bedwars\commands\BuildCommand;
use battleoase\bedwars\commands\SetupCommand;
use battleoase\bedwars\listener\BlockBreakListener;
use battleoase\bedwars\listener\EntityDamageListener;
use battleoase\bedwars\listener\InventoryPickupItemListener;
use battleoase\bedwars\listener\PlayerInteractListener;
use battleoase\bedwars\listener\PlayerJoinListener;
use battleoase\bedwars\listener\PlayerQuitListener;
use battleoase\bedwars\player\PlayerManager;
use battleoase\bedwars\shop\CategoryManager;
use battleoase\bedwars\shop\types\ShopCategory;
use battleoase\bedwars\shop\types\ShopItem;
use battleoase\bedwars\task\GameTask;
use battleoase\bedwars\task\ScoreTask;
use battleoase\bedwars\utils\ConfigLoader;
use battleoase\bedwars\utils\PlayerScoreboard;
use BattleOase\FFA\EventListener;
use ceepkev77\cloudbridge\network\DataPacket;
use ceepkev77\cloudbridge\network\packet\UpdateGameServerInfoPacket;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\block\BlockLegacyIds;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\Bed;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\Task;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class BedWars extends PluginBase
{

	const PREFIX = "§cBedWars §r§f§8× §7";

	/**
	 * @var BedWars
	 */
	private static BedWars $instance;
	private PlayerManager $playerManager;
	private PlayerScoreboard $playerScoreboard;

	public bool $ingame = false;
	public bool $saveDamager = false;

	public bool $ranked = true;

	public bool $statsMessage = false;

	public array $lastdamager = [];
	public array $goldvote = [];
	public array $bed = [];
	public array $stack = [];

	public int $countdown = 20;

	public static int $i = 0;
	public int $mode = 0;

	public int $no = 0;
	public int $yes = 0;

	const LOBBY_ITEMS = [
		[
			"id" => ItemIds::GOLD_INGOT,
			"name" => "§8● §7Gold Voting",
			"meta" => 1,
			"count" => 1,
			"slot" => 0,
			"enchantments" => [],
		],
		[
			"id" => ItemIds::MAP,
			"name" => "§8● §7Map Voting",
			"meta" => 1,
			"count" => 1,
			"slot" => 1,
			"enchantments" => [],
		],
		[
			"id" => ItemIds::NETHER_STAR,
			"name" => "§8● §7Select Team",
			"meta" => 1,
			"count" => 1,
			"slot" => 2,
			"enchantments" => [],
		],
		[
			"id" => ItemIds::SLIME_BALL,
			"name" => "§8● §eHub",
			"meta" => 1,
			"count" => 1,
			"slot" => 4,
			"enchantments" => [],
		]
	];

	const END_ITEMS = [
		[
			"id" => ItemIds::COMPASS,
			"name" => "§8● §eHub",
			"meta" => 1,
			"count" => 1,
			"slot" => 0,
			"enchantments" => [],
		]
	];

	public function loadContents(array $items) : array
	{
		$contents = [];
		foreach ($items as $item) {
			$slot =  $item["slot"];
			$item = $this->getItemByItemData($item);
			$contents[$slot] = $item;
		}
		return $contents;

	}

	public function getItemByItemData(array $data) {
		$count = $data["count"];
		if($count != str_replace("-", "", $count)) {
			$countArray = explode("-", $count);
			$count = rand(... $countArray);
		}
		$item  = ItemFactory::getInstance()->get($data["id"], $data["meta"], $count);
		if($item->getMaxStackSize() < $count) {
			$item->setCount($item->getMaxStackSize());
		}
		if(isset($data["name"])) {
			$item->setCustomName($data["name"]);
		}
		if(isset($data["enchantment"])) {
			foreach ($data["enchantment"] as $enachantment) {
				$enachantment_data = explode(":", $enachantment);
				$e = EnchantmentIdMap::getInstance()->fromId(($enachantment_data[0]));
				$item->addEnchantment(new EnchantmentInstance($e, $enachantment_data[1]));
			}
		}
		return $item;
	}


	const BLOCKS = [
		BlockLegacyIds::SANDSTONE, BlockLegacyIds::END_STONE, BlockLegacyIds::GLASS,
		BlockLegacyIds::CHEST, BlockLegacyIds::IRON_BLOCK, BlockLegacyIds::COBWEB
	];


	public function onEnable(): void
	{
		$this->playerManager = new PlayerManager();

		self::$instance = $this;

		BedWars::getInstance()->getScheduler()->scheduleDelayedTask(new class() extends Task{
			public function onRun(): void
			{
				$packet = new UpdateGameServerInfoPacket();
				$packet->type = $packet->TYPE_UPDATE_STATE_MODE;
				$packet->value = 0;
				$packet->sendPacket();
			}
		}, 30);

		$this->playerScoreboard = new PlayerScoreboard();

		if(!file_exists($this->getDataFolder() . "config.yml")) {
			$this->saveResource("config.yml");
		}

		if(!InvMenuHandler::isRegistered()){
			InvMenuHandler::register($this);
		}
		$loader = new ConfigLoader();
		$loader->load($this->getDataFolder());
		$this->getServer()->getPluginManager()->registerEvents(new PlayerJoinListener(), $this);
		$this->getServer()->getPluginManager()->registerEvents(new PlayerQuitListener(), $this);
		$this->getServer()->getPluginManager()->registerEvents(new BlockBreakListener(), $this);
		$this->getServer()->getPluginManager()->registerEvents(new EntityDamageListener(), $this);
		$this->getServer()->getPluginManager()->registerEvents(new PlayerInteractListener(), $this);
		$this->getServer()->getPluginManager()->registerEvents(new InventoryPickupItemListener(), $this);

		$this->getScheduler()->scheduleRepeatingTask(new GameTask(), 20);
		//$this->getScheduler()->scheduleRepeatingTask(new TestGameTask(), 20);
		$this->getScheduler()->scheduleRepeatingTask(new ScoreTask(), 20);

		$this->getServer()->getCommandMap()->register("setup", new SetupCommand());
		$this->getServer()->getCommandMap()->register("build", new BuildCommand());

		$this->loadShop();

		$this->statsMessage = false;
	}

	public function onDisable(): void
	{
		$this->statsMessage = false;
		BedWars::getInstance()->getScheduler()->cancelAllTasks();
	}

	/**
	 * @return BedWars
	 */
	public static function getInstance(): BedWars
	{
		return self::$instance;
	}


	private function loadShop(): void{
		$config = new Config("/home/cloud/data/bedwars/shopdb.json", Config::JSON);
		if(!is_file("/home/cloud/data/bedwars/shopdb.json")) {
			$config = new Config("/home/cloud/data/bedwars/shopdb.json", Config::JSON);
			$config->set("categories", ["RushCategory" => ["showName" => "Â§eRushCategory", "slotInChest" => 4, "itemId" => ItemIds::CLOCK, "itemMeta" => 0, "items" => ["stick " => ["itemId" => ItemIds::STICK, "itemMeta" => 0, "itemCount" => 0, "enchantments" => [EnchantmentIds::KNOCKBACK . ":1", EnchantmentIds::UNBREAKING . ":10"], "slotInChest" => 18, "priceType" => "bronze", "priceAmount" => 8]]]]);
			$config->save();
		}

		foreach (array_keys($config->get("categories")) as $categoryKey) {
			$e = $config->get("categories")[$categoryKey];
			$categoryName = str_replace("&", TextFormat::ESCAPE, $e["showName"]);

			$categoryItem = ItemFactory::getInstance()->get($e["itemId"], $e["itemMeta"])->setCustomName($categoryName);
			$categoryItem->getNamedTag()->setString("category", $categoryKey);

			$slotInChestCategory = $e["slotInChest"];
			$shopItems = [];
			foreach (array_keys($e["items"]) as $itemKey) {
				$itemData = $e["items"][$itemKey];
				$item = ItemFactory::getInstance()->get($itemData["itemId"], $itemData["itemMeta"], $itemData["itemCount"]);
				$item->getNamedTag()->setShort("priceAmount", $itemData["priceAmount"]);
				$item->getNamedTag()->setString("priceType", $itemData["priceType"]);
				if($itemData["priceType"] == "bronze") {
					$item->setLore(["  x" . $itemData["priceAmount"]]); //<-
				}
				foreach ($itemData["enchantments"] as $enchantInfo) {
					$enchantData = explode(":", $enchantInfo);
					$enchantment = new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId($enchantData[0]), $enchantData[1]);
					$item->addEnchantment($enchantment);
				}
				$shopItems[] = new ShopItem($item, $itemData["slotInChest"]);
			}

			CategoryManager::registerCategory(new ShopCategory($categoryKey, $categoryItem, $slotInChestCategory, $shopItems));
		}
	}

	/**
	 * @return PlayerManager
	 */
	public function getPlayerManager(): PlayerManager
	{
		return $this->playerManager;
	}

	/**
	 * @return PlayerScoreboard
	 */
	public function getPlayerScoreboard(): PlayerScoreboard
	{
		return $this->playerScoreboard;
	}

	public function getArena()
	{
		return MapCache::randomMap();
	}

	/**
	 * @return bool
	 */
	public function isRanked(): bool
	{
		return $this->ranked;
	}

	/**
	 * @param bool $ranked
	 */
	public function setRanked(bool $ranked): void
	{
		$this->ranked = $ranked;
	}
}