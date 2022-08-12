<?php


namespace syntaxpe\knockffa\events;


use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\world\sound\ClickSound;
use pocketmine\world\sound\EndermanTeleportSound;
use pocketmine\world\sound\GhastShootSound;
use syntaxpe\knockffa\KnockFFA;
use syntaxpe\knockffa\player\KnockPlayer;

class PlayerJoinListener implements Listener
{

	public function onJoin(PlayerJoinEvent $event) {
		$player = $event->getPlayer();
		$name  = $player->getName();

		KnockFFA::getInstance()->getPlayerManager()->registerPlayer(new KnockPlayer($event->getPlayer()));
		$knockPlayer = KnockFFA::getInstance()->getPlayerManager()->getPlayer($event->getPlayer()->getName());
		$knockPlayer->onLoad();

		$this->giveItems($player);


		$player->getWorld()->addSound(new Vector3($player->getPosition()->getX(), $player->getPosition()->getY(), $player->getPosition()->getZ()), new GhastShootSound());
		$player->getWorld()->addSound(new Vector3($player->getPosition()->getX(), $player->getPosition()->getY(), $player->getPosition()->getZ()), new EndermanTeleportSound());
	}

	public function onQuit(PlayerQuitEvent $event) {
		$player = $event->getPlayer();
		$player->getInventory()->clearAll();
	}

	public function onRespawn(Player $player) {
		$player->setHealth(20);
	}

	public function onKill(Player $player) {
		$player->setHealth(20);

	}

	public function giveItems(Player $player)
	{
		$player->getInventory()->clearAll();
		$player->setHealth(20);

		$player->getInventory()->setItem(0, ItemFactory::getInstance()->get(ItemIds::STICK)->setCustomName("§7Stick")->addEnchantment(new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::KNOCKBACK), 1)));

		$player->getInventory()->setItem(1, ItemFactory::getInstance()->get(261, 0, 1)->setCustomName("§7Bow"));

		$player->getInventory()->setItem(8, ItemFactory::getInstance()->get(262, 0, 16)->setCustomName("§7Arrows"));

		$player->getWorld()->addSound($player->getPosition()->asVector3(), new ClickSound());
	}

	public function onBreak(BlockBreakEvent $event) {
		$player = $event->getPlayer();
		if($player->getGamemode() === GameMode::CREATIVE()) {
			$event->uncancel();
		}else {
			$event->cancel();
		}
	}

	public function onPlace(BlockPlaceEvent $event) {
		$player = $event->getPlayer();
		if($player->getGamemode() === GameMode::CREATIVE()) {
			$event->uncancel();
		}else {
			$event->cancel();
		}
	}

	public function onDrop(PlayerDropItemEvent $event) {
		$player = $event->getPlayer();
		if($player->getGamemode() === GameMode::CREATIVE()) {
			$event->uncancel();
		}else {
			$event->cancel();
		}
	}
}