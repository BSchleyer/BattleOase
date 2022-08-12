<?php


namespace battleoase\lobbycore\events;


use battleoase\battlecore\customInteractSystem\events\PlayerInteractEventWithDelay;
use battleoase\lobbycore\LobbyCore;
use battleoase\lobbycore\player\PlayerManager;
use battleoase\lobbycore\utils\SettingUtils;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\sound\PopSound;

class SecurityPlayerEvents implements Listener
{

	public function onFallDamage(EntityDamageEvent $event){

		if($event->getCause() == EntityDamageEvent::CAUSE_FALL) {
			$event->cancel();
		}
	}

	public function onThing(EntityDamageByEntityEvent $event){

		$event->cancel();
	}

	public function onItemHold(PlayerItemHeldEvent $event){

		$player = $event->getPlayer();
		if (SettingUtils::get($player->getName())["hotbarSounds"]){
			$player->getWorld()->addSound(new Vector3($player->getPosition()->getX(), $player->getPosition()->getY(), $player->getPosition()->getZ()),new PopSound(), [$player]);
		}

	}

	public function onInvetoryPickupItem(EntityItemPickupEvent $event)
	{
		$player = $event->getEntity();
		if ($player instanceof Player){
			$lobbyPlayer = PlayerManager::getPlayer($player->getName());
			if($lobbyPlayer->isBuild() == true) {
				$event->uncancel();
			} else {
				$event->cancel();
			}
		}
	}

	public function onEntityDamage(EntityDamageEvent $event): void
	{
		$event->cancel();
	}

	public function onPlayerDropItem(PlayerDropItemEvent $event)
	{
		$lobbyPlayer = PlayerManager::getPlayer($event->getPlayer()->getName());
		if($lobbyPlayer->isBuild() == true) {
			$event->uncancel();
		} else {
			$event->cancel();
		}
	}


	public function onBreak(BlockBreakEvent $event){
		$lobbyPlayer = PlayerManager::getPlayer($event->getPlayer()->getName());
		if($lobbyPlayer->isBuild()) {
			$event->uncancel();
		} else {
			$event->cancel();
		}
	}

	public function onTransaction(InventoryTransactionEvent $event) {
		$player = $event->getTransaction()->getSource();
		if (!$player->isCreative(true)) {
			$event->cancel();
		}
	}

	public function PlayerPlaceEvent(BlockPlaceEvent $event) {
	$lobbyPlayer = PlayerManager::getPlayer($event->getPlayer()->getName());
	if($lobbyPlayer->isBuild()) {
			$event->uncancel();
		} else {
			if(LobbyCore::getInstance()->getBridge()->bridge[$event->getPlayer()->getName()] == true) {
				$event->uncancel();
				$block = $event->getBlock();
				$x = $block->getPosition()->getX();
				$y = $block->getPosition()->y;
				$z = $block->getPosition()->z;
				$blocks = LobbyCore::getInstance()->getBridge()->blocks;
				$blocks[] = $x . ':' . $y . ':' . $z;
				LobbyCore::getInstance()->getBridge()->blocks = $blocks;
			} else {
				$event->cancel();
			}
		}
	}

	public function onHunger(PlayerExhaustEvent $event) {
		$event->cancel();
	}

	public function onDrop(PlayerDropItemEvent $event){
		$event->cancel();
	}

	public function onInv(InventoryTransactionEvent $event){
		$player = $event->getTransaction()->getSource();
		$lobbyPlayer = PlayerManager::getPlayer($player->getName());
		if($lobbyPlayer->isBuild()) {
			$event->uncancel();
		} else {
			$event->cancel();
		}
	}
}