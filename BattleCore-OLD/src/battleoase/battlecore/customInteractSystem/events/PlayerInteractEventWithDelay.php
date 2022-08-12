<?php

namespace battleoase\battlecore\customInteractSystem\events;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\event\Cancellable;
use pocketmine\event\player\PlayerEvent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\Position;

class PlayerInteractEventWithDelay extends PlayerEvent
{
    public const LEFT_CLICK_BLOCK = 0;
    public const RIGHT_CLICK_BLOCK = 1;
    public const LEFT_CLICK_AIR = 2;
    public const RIGHT_CLICK_AIR = 3;
    public const PHYSICAL = 4;

    /** @var Block */
    protected Item|Block|null|\pocketmine\entity\Attribute $blockTouched;

    /** @var Vector3 */
    protected Vector3 $touchVector;

    /** @var int */
    protected int $blockFace;

    /** @var Item */
    protected Item $item;

    /** @var int */
    protected int $action;

    public array $delay = [];

    public function __construct(Player $player, Item $item, ?Block $block, ?Vector3 $touchVector, int $face, int $action = PlayerInteractEventWithDelay::RIGHT_CLICK_BLOCK)
    {
        assert($block !== null or $touchVector !== null);
        $this->player = $player;
        $this->item = $item;
        $this->blockTouched = $block ?? BlockFactory::getInstance()->get(0, 0, new Position(0, 0, 0, $player->getWorld()));
        $this->touchVector = $touchVector ?? new Vector3(0, 0, 0);
        $this->blockFace = $face;
        $this->action = $action;
    }

    public function getAction(): int
    {
    	return $this->action;
    }

    public function getItem(): Item
    {
    	return $this->item;
    }

    public function getBlock(): Block
    {
    	return $this->blockTouched;
    }

    public function getTouchVector(): Vector3
    {
        return $this->touchVector;
    }

    public function getFace(): int
    {
        return $this->blockFace;
    }
}

