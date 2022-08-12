<?php


namespace battleoase\bedwars\shop\types;


use pocketmine\item\Item;

class ShopCategory
{
    /** @var string */
    private string $name;
    /** @var Item */
    private Item $item;
    /** @var int */
    private int $slotInChest;
    /** @var ShopItem[] */
    private array $items;
    /** @var Item[] */
    private array $contents;

    /**
     * ShopCategory constructor.
     *
     * @param string $name
     * @param \pocketmine\item\Item $item
     * @param int $slotInChest
     * @param array $items
     */
    public function __construct(string $name, Item $item, int $slotInChest, array $items)
    {
        $this->items = $items;
        $this->name = $name;
        $this->slotInChest = $slotInChest;
        $this->item = $item;
        /** @var ShopItem $item */
        foreach ($items as $item)
            $this->contents[$item->getSlotInChest()] = $item->getItem();

    }

    /**
     * @return ShopItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return int
     */
    public function getSlotInChest(): int
    {
        return $this->slotInChest;
    }

    /**
     * @return \pocketmine\item\Item
     */
    public function getItem(): \pocketmine\item\Item
    {
        return $this->item;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return \pocketmine\item\Item[]
     */
    public function getContents(): array
    {
        return $this->contents;
    }
}