<?php


namespace syntaxpe\knockffa\player;


use pocketmine\player\Player;

class KnockPlayer
{

    /**
     * @var Player
     */
    private Player $player;

	#private ShopMenu $shopMenu;

	public function __construct(Player $player)
    {
        $this->player = $player;
    }

    public function onLoad()
    {
		#$this->setShopMenu(new ShopMenu());
    }

	/**
     * @param Player $player
     */
    public function setPlayer(Player $player): void
    {
        $this->player = $player;
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

	public function teleport()
	{
		/*Server::getInstance()->getWorldManager()->loadWorld($map);
		$this->player->teleport(new Position($x, $y, $z, Server::getInstance()->getWorldManager()->getWorldByName($map)));*/

		$this->player->getInventory()->clearAll();
		$this->player->getXpManager()->setXpLevel(0);
		$this->player->getHungerManager()->setFood(20);
		$this->player->setHealth(20);
    }
}