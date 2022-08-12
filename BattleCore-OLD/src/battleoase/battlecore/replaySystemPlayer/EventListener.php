<?php

namespace battleoase\battlecore\replaySystemPlayer;

use battleoase\battlecore\BattleCore;
use battleoase\battlecore\customInteractSystem\events\PlayerInteractEventWithDelay;
use battleoase\battlecore\replaySystemPlayer\events\EntityMoveEvent;
use battleoase\battlecore\replaySystemPlayer\gui\teleportToPlayerGui;
use battleoase\battlecore\replaySystemPlayer\manager\Replay;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\event\block\BlockItemPickupEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;
use pocketmine\world\sound\ClickSound;

class EventListener implements Listener {


    const PLAY_REPLAY_ITEMS = [
        [
            "id" => ItemIds::COMPASS,
            "name" => "§6Teleporter",
            "meta" => 0,
            "count" => 1,
            "slot" => 0,
            "enchantments" => [],
        ],
        [
            "id" => ItemIds::BLAZE_ROD,
            "name" => "§7-§610 Sekunden",
            "meta" => 0,
            "count" => 1,
            "slot" => 1,
            "enchantments" => [],
        ],
        [
            "id" => ItemIds::ARROW,
            "name" => "§6Speed down",
            "meta" => 0,
            "count" => 1,
            "slot" => 3,
            "enchantments" => [],
        ],
        [
            "id" => ItemIds::REDSTONE_TORCH,
            "name" => "§6Pause",
            "meta" => 0,
            "count" => 1,
            "slot" => 4,
            "enchantments" => [],
        ],
        [
            "id" => ItemIds::ARROW,
            "name" => "§6Speed up",
            "meta" => 0,
            "count" => 1,
            "slot" => 5,
            "enchantments" => [],
        ],
        [
            "id" => ItemIds::BLAZE_ROD,
            "name" => "§7+§610 Sekunden",
            "meta" => 0,
            "count" => 1,
            "slot" => 7,
            "enchantments" => [],
        ],
        [
            "id" => ItemIds::WOODEN_DOOR,
            "name" => "§cQuit replay",
            "meta" => 0,
            "count" => 1,
            "slot" => 8,
            "enchantments" => [],
        ]

    ];
    const PAUSE_REPLAY_ITEMS = [
        [
            "id" => ItemIds::COMPASS,
            "name" => "§6Teleporter",
            "meta" => 0,
            "count" => 1,
            "slot" => 0,
            "enchantments" => [],
        ],
        [
            "id" => ItemIds::BLAZE_ROD,
            "name" => "§7-§610 Sekunden",
            "meta" => 0,
            "count" => 1,
            "slot" => 1,
            "enchantments" => [],
        ],
        [
            "id" => ItemIds::ARROW,
            "name" => "§6Speed down",
            "meta" => 0,
            "count" => 1,
            "slot" => 3,
            "enchantments" => [],
        ],
        [
            "id" => ItemIds::EMERALD,
            "name" => "§6Play",
            "meta" => 0,
            "count" => 1,
            "slot" => 4,
            "enchantments" => [],
        ],
        [
            "id" => ItemIds::ARROW,
            "name" => "§6Speed up",
            "meta" => 0,
            "count" => 1,
            "slot" => 5,
            "enchantments" => [],
        ],
        [
            "id" => ItemIds::BLAZE_ROD,
            "name" => "§7+§610 Sekunden",
            "meta" => 0,
            "count" => 1,
            "slot" => 7,
            "enchantments" => [],
        ],
        [
            "id" => ItemIds::WOODEN_DOOR,
            "name" => "§cQuit replay",
            "meta" => 0,
            "count" => 1,
            "slot" => 8,
            "enchantments" => [],
        ]

    ];

    /**
     * EventListener constructor.
     */

    public function __construct() {
        Server::getInstance()->getPluginManager()->registerEvents($this, BattleCore::getInstance());
    }

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

    /**
     * @param PlayerJoinEvent $event
     */

    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        $player->saveNBT();
        $event->setJoinMessage("");
        $inv = $player->getInventory();
        $inv->clearAll();
        $player->setGamemode(GameMode::ADVENTURE());
        $player->setGamemode(GameMode::SPECTATOR());

        $replay = BattleCore::getInstance()->replaySystemPlayer->getReplay();


        if(BattleCore::getInstance()->replaySystemPlayer->getReplay()->isRunning()) {
            $player->teleport(new Position(0, 100, 0, Server::getInstance()->getWorldManager()->getWorldByName("replayworld")));
            $player->teleport(BattleCore::getInstance()->replaySystemPlayer->getReplay()->getSpectatorSpawn());
            return;
        }
        $inv = $player->getInventory();
        $inv->setContents(($replay->isPaused() ? $this->loadContents(self::PLAY_REPLAY_ITEMS) : $this->loadContents(self::PAUSE_REPLAY_ITEMS)));

        //$packet = new PacketRequests("Lobby", 0x00122, "id");
        //$replayId = $packet->get("id", $packet->decode($packet->requestData("replayID")));

		//if(is_dir("/home/cloud/data/replaysystem/$replayId")) {
		//	BattleCore::getInstance()->replaySystemPlayer->getReplay()->playReplay($replayId);
		////    $player->getWorld()->setTime(0);
		//	BattleCore::getInstance()->replaySystemPlayer->getReplay()->setPlayType(Replay::REPLAY_PAUSED);
		//} else {
	//    BattleCore::getInstance()->getServer()->shutdown();
		//}

    }

    public function interact(PlayerInteractEventWithDelay $eventWithDelay) {
        $player = $eventWithDelay->getPlayer();
        $item = $eventWithDelay->getItem();
        $name = $item->getCustomName();
        if ($player instanceof Player){
			$replay = BattleCore::getInstance()->replaySystemPlayer->getReplay();
			switch ($name) {
				case "§6Teleporter":
					$player->sendForm(new teleportToPlayerGui());
					$player->getWorld()->addSound($player->getPosition()->asVector3(), new ClickSound(), [$player]);
					break;
				case "§7-§610 Sekunden":
					if(($replay->currentTick - 200) <= 1) {
						$replay->setNextTick(1);
					} else {
						$replay->setNextTick($replay->currentTick - 200);
					}
					$player->getWorld()->addSound($player->getPosition()->asVector3(), new ClickSound(), [$player]);
					break;
				case "§6Speed down":
					$replay->setSpeed($replay->getSpeed() - 1);
					$player->sendMessage($replay->getSpeed() +1 . "x");
					$player->getWorld()->addSound($player->getPosition()->asVector3(), new ClickSound(), [$player]);
					break;
				case "§6Pause":
					$replay->setPlayType(Replay::REPLAY_PAUSED);
					$player->getInventory()->setContents($this->loadContents(self::PAUSE_REPLAY_ITEMS));
					$player->getWorld()->addSound($player->getPosition()->asVector3(), new ClickSound(), [$player]);
					break;
				case "§6Play":
					$replay->setPlayType(Replay::REPLAY_NORMAL);
					$player->getInventory()->setContents($this->loadContents(self::PLAY_REPLAY_ITEMS));
					$player->getWorld()->addSound($player->getPosition()->asVector3(), new ClickSound(), [$player]);
					break;
				case "§6Speed up":
					$replay->setSpeed($replay->getSpeed() + 1);
					$player->sendMessage($replay->getSpeed() +1 . "x");
					$player->getWorld()->addSound($player->getPosition()->asVector3(), new ClickSound(), [$player]);
					break;
				case "§7+§610 Sekunden":
					if(($replay->currentTick + 200) > $replay->getLastTick()) {
						$replay->setNextTick($replay->getLastTick());
					} else {
						$replay->setNextTick($replay->currentTick + 200);
					}
					$player->getWorld()->addSound($player->getPosition()->asVector3(), new ClickSound(), [$player]);
					break;
				case "§cQuit replay":
					$player->kick("FALLBACK");
					$player->getWorld()->addSound($player->getPosition()->asVector3(), new ClickSound(), [$player]);
					break;
			}
		}
    }

    /**
     * @param PlayerQuitEvent $event
     */

    public function onQuit(PlayerQuitEvent $event) {
        $event->setQuitMessage("");

    }

    /**
     * @param EntityDamageEvent $event
     */

    public function onDamage(EntityDamageEvent $event) {
        $event->cancel();
    }

    /**
     * @param PlayerExhaustEvent $event
     */

    public function onExhaust(PlayerExhaustEvent $event) {
        $event->getPlayer()->getHungerManager()->setFood(19);
        $event->getPlayer()->getHungerManager()->setFood(20);
        $event->cancel();
    }

    public function onEntityMove(EntityMoveEvent $event): void {
        if($event->getEntity() instanceof Player) {
            return;
        }
        if(BattleCore::getInstance()->replaySystemPlayer->getReplay()->isPaused()) {
            $event->cancel();
        }
    }

    /**
     * @param PlayerDropItemEvent $event
     */

    public function onDrop(PlayerDropItemEvent $event) {
        $event->cancel();
    }

    /**
     * @param InventoryTransactionEvent $event
     */

    public function onTransaction(InventoryTransactionEvent $event) {
        $event->cancel();
    }

    /**
     * @param EntityItemPickupEvent $event
     */

    public function onPickUp(EntityItemPickupEvent $event) {
        $event->cancel();
    }

    /**
     * @param BlockItemPickupEvent $event
     */

    public function arrowPickup(BlockItemPickupEvent $event) {
        $event->cancel();
    }

    /**
     * @param EntityExplodeEvent $event
     */

    public function onExplode(EntityExplodeEvent $event): void {
        $event->setBlockList([]);
        $event->cancel();
    }

}