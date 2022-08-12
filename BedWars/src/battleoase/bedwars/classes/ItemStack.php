<?php

namespace battleoase\bedwars\classes;

use BattleOase\BedWars\BedWars;
use ceepkev77\BattleCore\subsystems\plugin\worldSystem\world\World;
use pocketmine\block\ItemFrame;
use pocketmine\entity\object\ItemEntity;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;

/**
 * Class ItemStack
 * @package xxAROX\BedWarsCommand\utils
 * @author BauboLP,xxAROX
 * @date 03.06.2020 - 21:48
 * @project BedWarsCommand
 */
class ItemStack{
	/** @var string */
	private $arena;
	/** @var Vector3 */
	private $position;
	/** @var int  */
	private $count = 0;
	/** @var int */
	private $resource;
	/** @var ItemEntity */
	private $item;


	/**
	 * ItemStack constructor.
	 * @param string $arena
	 * @param Vector3 $position
	 * @param int $resource
	 */
	public function __construct(string $arena, Vector3 $position, int $resource){
		$this->arena = $arena;
		$this->position = $position;
		$this->resource = $resource;
	}

	/**
	 * Function getPosition
	 * @return Vector3
	 */
	public function getPosition(): Vector3{
		return $this->position;
	}

	/**
	 * Function getPositionAsString
	 * @return string
	 */
	public function getPositionAsString(): string{
		return "{$this->position->x}:{$this->position->y}:{$this->position->z}";
	}

	/**
	 * Function getArena
	 * @return string
	 */
	public function getArena(): string{
		return $this->arena;
	}

	/**
	 * Function getCount
	 * @return int
	 */
	public function getCount(): int{
		return $this->count;
	}

	/**
	 * Function getResource
	 * @return int
	 */
	public function getResource(): int{
		return $this->resource;
	}

	/**
	 * Function setCount
	 * @param int $count
	 * @return void
	 */
	public function setCount(int $count): void{
		$this->count = $count;
		$this->item->setNameTag($this->getColor() . $this->item->getItem()->getCount());
	}

	/**
	 * Function getItem
	 * @return ItemEntity
	 */
	public function getItem(): ItemEntity{
		if (is_null($this->item)) {
			$this->spawnResource();
		}
		return $this->item;
	}

	/**
	 * Function addCount
	 * @param int $count
	 * @return void
	 */
	public function addCount(int $count=1): void{
		$itemEntity = $this->item;
		$this->setCount($this->getCount() +$count);
		$itemEntity->getItem()->setCount($this->getCount());
		$itemEntity->setNameTag($this->getColor() . $itemEntity->getItem()->getCount());
	}

	/**
	 * Function spawnResource
	 * @return void
	 */
	public function spawnResource(){
		$resource = ItemFactory::getInstance()->get($this->resource, 0, 1);
		if (BedWars::getInstance()->getServer()->getWorldManager()->getWorldByName($this->arena) instanceof \pocketmine\world\World) {
			$itemEntity = BedWars::getInstance()->getServer()->getWorldManager()->getWorldByName($this->arena)->dropItem($this->getPosition()->asVector3(), $resource, new Vector3(0, 0, 0));
			$itemEntity->setOwner($this->getPositionAsString());
			$itemEntity->setNameTagAlwaysVisible(true);
			$itemEntity->setNameTag("{$this->getColor()}{$this->getCount()}");
			$this->item = $itemEntity;
		}
	}

	/**
	 * Function getColor
	 * @return string
	 */
	private function getColor(): string{
		if ($this->resource == ItemIds::BRICK)
			return "§c";
		else if ($this->resource == ItemIds::IRON_INGOT)
			return "§7";
		else if ($this->resource == ItemIds::GOLD_INGOT)
			return "§g";
		else if ($this->resource == ItemIds::DIAMOND)
			return "§b";
		else if ($this->resource == ItemIds::EMERALD)
			return "§a";
		else if ($this->resource == ItemIds::COAL)
			return "§0";
		else
			return "§f";
	}
}
