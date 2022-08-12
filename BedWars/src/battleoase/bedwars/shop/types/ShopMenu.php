<?php


namespace battleoase\bedwars\shop\types;


use battleoase\battlecore\BattleCore;
use battleoase\bedwars\BedWars;
use battleoase\bedwars\shop\CategoryManager;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class ShopMenu
{
    /** @var InvMenu */
    private InvMenu $menu;

    public function __construct()
    {
        $this->menu = InvMenu::create(InvMenu::TYPE_CHEST)
            ->setName(BedWars::PREFIX.TextFormat::YELLOW."Shop")
              ->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult{
                  $player = $transaction->getPlayer();
                  $clicked = $transaction->getItemClicked();

                  $bwPlayer = BedWars::getInstance()->getPlayerManager()->getPlayer($player);

                  if($bwPlayer === null) return $transaction->discard();

                  $namedTag = $clicked->getNamedTag();
                  $priceAmount = $namedTag->getShort("priceAmount", 99);
                  $priceType = $namedTag->getString("priceType", "error");
                  $category = $namedTag->getString("category", "error");

                  if($category != "error") {
                      $category = CategoryManager::getCategories()[$category];
                      $bwPlayer->getShopMenu()->updateCategory($category);
                      return $transaction->discard();
                  }

                  if($category == "error") {
                      if($priceAmount >= 64 || $priceType == "error") {
                          $player->sendMessage(TextFormat::RED."ERROR: SHOPITEM_CONFIGURATION_WRONG");
                          return $transaction->discard();
                      }
                  }

                  $neededItem = -1;
                  if(strtolower($priceType) === "bronze")
                      $neededItem = ItemIds::BRICK;
                  elseif(strtolower($priceType) === "iron" || strtolower($priceType) === "eisen")
                      $neededItem = ItemIds::IRON_INGOT;
                  else if(strtolower($priceType) === "gold")
                      $neededItem = ItemIds::GOLD_INGOT;

                  if($neededItem === -1) {
                      $player->sendMessage(TextFormat::RED."ERROR: SHOPITEM_PRICE_TYPE_WRONG");
                      return $transaction->discard();
                  }

                   $canBuy = CategoryManager::setPrice($player, $priceAmount, $neededItem);

                  if($canBuy) {
                      CategoryManager::rm($player, $neededItem, $priceAmount);

                      $player->getInventory()->addItem(clone $clicked);

                      $pk = new PlaySoundPacket();
                      $pk->soundName = "random.pop";
                      $pk->volume = 10;
                      $pk->x = $player->getPosition()->getX();
                      $pk->y = $player->getPosition()->getY();
                      $pk->z = $player->getPosition()->getZ();
                      $pk->pitch = 1;
                  }else {
					  $pk = new PlaySoundPacket();
					  $pk->soundName = "note.bass";
					  $pk->volume = 10;
					  $pk->x = $player->getPosition()->getX();
					  $pk->y = $player->getPosition()->getY();
					  $pk->z = $player->getPosition()->getZ();
					  $pk->pitch = 1;

                      BattleCore::getInstance()->getLanguageSystem()->translate($player, "not.enough.resources");
                  }
                  return $transaction->discard();
              });
    }

    /**
     * @param ShopCategory $category
     */
    public function updateCategory(ShopCategory $category)
    {
        $inventory = $this->menu->getInventory();
        for($i = 0; $i < 27; $i++)
            $inventory->setItem($i, ItemFactory::getInstance()->get(ItemIds::AIR, 0, 1));

        foreach ($category->getContents() as $slot => $item) {
            $inventory->setItem($slot, $item);
        }

        $this->listCategories();
    }

    public function listCategories(): void
    {
        foreach (CategoryManager::getCategories() as $category)
            $this->menu->getInventory()->setItem($category->getSlotInChest(), $category->getItem());
    }

    /**
     * @param Player $player
     */
    public function send(Player $player)
    {
        $category = CategoryManager::getCategories()["RushCategory"];
        $this->updateCategory($category);
        $this->menu->send($player);
    }
}