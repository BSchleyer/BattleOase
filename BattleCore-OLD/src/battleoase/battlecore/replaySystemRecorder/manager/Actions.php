<?php

namespace battleoase\battlecore\replaySystemRecorder\manager;

use battleoase\battlecore\utils\math;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\Server;

class Actions {

	/** @var Replay|null */
	private ?Replay $replay = null;

	/** @var int  */
	private int $tick = 0;

	/**
	 * Actions constructor.
	 * @param Replay $replay
	 */

	public function __construct(Replay $replay) {
		$this->replay = $replay;
		$this->tick = Server::getInstance()->getTick() - $this->replay->getStartTick();
	}

	/**
	 * @param Block $block
	 * @param bool $silent
	 */

	public function breakAction(Block $block, $silent = false) {
		$this->replay->actions["Break"][$this->tick][] = ["X" => $block->getPosition()->getX(), "Y" => $block->getPosition()->getY(), "Z" => $block->getPosition()->getZ(), "Id" => $block->getId(), "Meta" => $block->getMeta(), "Silent" => $silent];
	}

	/**
	 * @param Block $block
	 */

	public function placeAction(Block $block) {
		$this->replay->actions["Place"][$this->tick][] = ["X" => $block->getPosition()->getX(), "Y" => $block->getPosition()->getY(), "Z" => $block->getPosition()->getZ(), "Id" => $block->getId(), "Meta" => $block->getMeta()];
	}

	/**
	 * @param Player $player
	 */

	public function playerMoveAction(Player $player) {
		$this->replay->addEntity($player);
		$this->replay->actions["Move"][$this->tick][$player->getId()][] = ["X" => $player->getPosition()->getX(), "Y" => $player->getPosition()->getY(), "Z" => $player->getPosition()->getZ(), "Yaw" => $player->getPosition()->getY(), "Pitch" => $player->getLocation()->getPitch(), "OnFire" => $player->isOnFire(), "ID" => $player->getInventory()->getItemInHand()->getId()];
	}

	/**
	 * @param Entity $entity
	 */

	public function entityMoveAction(Entity $entity) {
		$this->replay->addEntity($entity);
		$this->replay->actions["Move"][$this->tick][$entity->getId()][] = ["X" => $entity->getPosition()->getX(), "Y" => $entity->getPosition()->getY(), "Z" => $entity->getPosition()->getZ(), "Yaw" => $entity->getPosition()->getY(), "Pitch" => $entity->getLocation()->getPitch()];
	}

	/**
	 * @param Entity $entity
	 */

	public function entityUpdateAction(Entity $entity) {
		$this->replay->addEntity($entity);
		$this->replay->actions["UpdateEntity"][$this->tick][$entity->getId()][] = [
			"Nametag" => $entity->getNameTag(),
			"Scale" => $entity->getScale()
		];
	}

	/**
	 * @param Player $player
	 * @param string $animation
	 */

	public function animateAction(Player $player, string $animation) {
		$this->replay->addEntity($player);
		$this->replay->actions["Animation"][$this->tick][$player->getId()][] = ["Item" => $player->getInventory()->getItemInHand()->getId(), "Type" => $animation];
	}

	/**
	 * @param Player $player
	 */

	public function damageAction(Player $player) {
		$this->replay->addEntity($player);
		$this->replay->actions["Damage"][$this->tick][$player->getId()][] = ["Item" => $player->getInventory()->getItemInHand()->getId()];
	}

	/**
	 * @param Player $player
	 * @param bool $sneaking
	 */

	public function sneakAction(Player $player, bool $sneaking) {
		$this->replay->addEntity($player);
		$this->replay->actions["Sneak"][$this->tick][$player->getId()][] = ["Item" => $player->getInventory()->getItemInHand()->getId(), "Sneaking" => $sneaking];
	}

	/**
	 * @param Player $player
	 */

	public function consumeItemAction(Player $player) {
		$this->replay->addEntity($player);
		$this->replay->actions["Consume"][$this->tick][$player->getId()][] = ["Item" => $player->getInventory()->getItemInHand()->getId()];
	}

	/**
	 * @param Player $player
	 */

	public function quitAction(Player $player) {
		$this->replay->actions["Quit"][$this->tick] = ["Id" => $player->getId()];
	}

	/**
	 * @param Player $player
	 */

	public function deathAction(Player $player) {
		$this->replay->addEntity($player);
		$this->replay->actions["Death"][$this->tick][$player->getId()] = ["Item" => $player->getInventory()->getItemInHand()->getId()];
	}

	/**
	 * @param Entity $entity
	 */

	public function despawnEntityAction(Entity $entity) {
		$this->replay->actions["DespawnEntity"][$this->tick][] = $entity->getId();
	}

	/**
	 * @param Block $block
	 * @param string $line1
	 * @param string $line2
	 * @param string $line3
	 * @param string $line4
	 */

	public function signChangeAction(Block $block, string $line1 = "", string $line2 = "", string $line3 = "", string $line4 = "") {
		$this->replay->actions["SignChange"][$this->tick] = ["X" => $block->getPosition()->getX(), "Y" => $block->getPosition()->getY(), "Z" => $block->getPosition()->getZ(), "Line1" => $line1, "Line2" => $line2, "Line3" => $line3, "Line4" => $line4];
	}

	/**
	 * @param Player $player
	 */

	public function itemHeldAction(Player $player) {
		$this->replay->addEntity($player);
		$this->replay->actions["ItemHeldUpdate"][$this->tick][$player->getId()] = [
			"ItemId" => $player->getInventory()->getItemInHand()->getId(),
			"ItemMeta" => $player->getInventory()->getItemInHand()->getMeta(),
			"Enchanted" => $player->getInventory()->getItemInHand()->hasEnchantments(),
			"Armor" => [
				"Boots" => $player->getArmorInventory()->getBoots()->getId().":".$player->getArmorInventory()->getBoots()->getMeta().":".$player->getArmorInventory()->getBoots()->hasEnchantments(),
				"Leggings" => $player->getArmorInventory()->getLeggings()->getId().":".$player->getArmorInventory()->getLeggings()->getMeta().":".$player->getArmorInventory()->getLeggings()->hasEnchantments(),
				"Chestplate" =>$player->getArmorInventory()->getChestplate()->getId().":".$player->getArmorInventory()->getChestplate()->getMeta().":".$player->getArmorInventory()->getChestplate()->hasEnchantments(),
				"Helmet" => $player->getArmorInventory()->getHelmet()->getId().":".$player->getArmorInventory()->getHelmet()->getMeta().":".$player->getArmorInventory()->getHelmet()->hasEnchantments()
			]
		];
	}

	/**
	 * @param Block $block
	 */

	public function onBlockUpdate(Block $block): void {
		$this->replay->actions["BlockUpdate"][$this->tick][] = ["X" => $block->getPosition()->getX(), "Y" => $block->getPosition()->getY(), "Z" => $block->getPosition()->getZ()];
	}

	/**
	 * @param string $message
	 */

	public function chatAction(string $message) {
		$this->replay->actions["Chat"][$this->tick][] = ["Message" => $message];
	}

	/**
	 * @param Vector3 $position
	 * @param int $evid
	 * @param int $data
	 */

	public function levelEventAction(Vector3 $position, int $evid, int $data): void {
		$this->replay->actions["LevelEventAction"][$this->tick][] = [
			"Position" => Math::vector3ToString($position),
			"EventId" => $evid,
			"Data" => $data
		];
	}
}