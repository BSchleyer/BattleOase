<?php


namespace battleoase\bedwars\shop;

use battleoase\bedwars\shop\types\ShopCategory;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class CategoryManager
{
    /** @var ShopCategory[]  */
    private static array $categories = [];

    /**
     * @return ShopCategory[]
     */
    public static function getCategories(): array
    {
        return self::$categories;
    }

    /**
     * @param ShopCategory $category
     */
    public static function registerCategory(ShopCategory $category): void
    {
        self::$categories[$category->getName()] = $category;
    }

    public static function rm(Player $player, int $id = ItemIds::BRICK, $count = 1)
    {
        $player->getInventory()->removeItem(ItemFactory::getInstance()->get($id, 0, $count));
    }

    public static function count(Player $player, int $id = ItemIds::BRICK): int{
        $all = 0;
        $inv = $player->getInventory();
        $content = $inv->getContents();
        foreach ($content as $item) {
            if ($item->getId() == $id) {
                $all += $item->getCount();
            }
        }
        return $all;
    }


    public static function addItem(Player $player, $id, $count, $name) {
        $item = ItemFactory::getInstance()->get($id, 0, $count)->setCustomName($name);
        $player->getInventory()->addItem($item);
    }

    public static function setPrice(Player $player, int $price, int $id) : bool {
        $woola = self::count($player, $id);
        if($woola < $price) {
            return false;
        } else {
            self::rm($player, $id, $price);
            return true;
        }
    }
}