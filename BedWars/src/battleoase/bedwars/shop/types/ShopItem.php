<?php


namespace battleoase\bedwars\shop\types;


use pocketmine\item\Item;

class ShopItem
{
    /** @var Item */
    private Item $item;
    /** @var int */
    private int $slotInChest;

    /**
     * ShopItem constructor.
     *
     * @param Item $item
     * @param int $slotInChest
     */
    public function __construct(Item $item, int $slotInChest)
    {
        $this->slotInChest = $slotInChest;
        $this->item = $item;
    }

    /**
     * @return \pocketmine\item\Item
     */
    public function getItem(): \pocketmine\item\Item
    {
        return $this->item;
    }

    /**
     * @return int
     */
    public function getSlotInChest(): int
    {
        return $this->slotInChest;
    }
}