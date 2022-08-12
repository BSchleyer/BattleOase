<?php

namespace battleoase\battlecore\npcSystem\classes;

use battleoase\battlecore\npcSystem\entities\CustomNPC;
use battleoase\battlecore\npcSystem\handler\NPCEventHandler;
use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\math\Vector3;

use InvalidArgumentException;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\inventory\WindowTypes;
use pocketmine\Server;
use pocketmine\world\World;

class NPCBuilder
{

	/**
	 * @var string
	 */
	private $name;


	/**
	 * @var CustomType
	 */
	private $type;

	/**
	 * @var Location
	 */
	private $position;


	/**
	 * @var NPCEventHandler
	 */
	private $handler = null;

    public function setType(CustomType $type): self
	{
		$this->type = $type;
		return $this;
	}


	public function setName(string $name): self
	{
		$this->name = str_replace("{LINE}", "\n", $name);
		return $this;
	}

	public function setPosition(Vector3 $position): self
	{
		$this->position = $position;
		return $this;
	}

	public function setHandler(NPCEventHandler $handler): self
	{
		$this->handler = $handler;
		return $this;
	}


	public function build(): CustomNPC
	{
		if ($this->position->getWorld() instanceof World) {
			if ($this->position instanceof Location) {
				if ($this->name !== null && $this->name !== "") {
				    $skin = new Skin(uniqid(), $this->getType()->getImageData(), "", $this->getType()->getGeometryName(), $this->getType()->getGeometry());
					$npc = new CustomNPC($this->getPosition(), $skin, null, false);
					$npc->setHeader($this->name);
					$npc->setHandler($this->handler ?? null);
					$npc->update();
					return $npc;
				}
			}
		}
		throw new InvalidArgumentException("It seems at least one of three (Level, Position, Header ) required arguments for your NPC Build is missing");
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return CustomType
	 */
	public function getType(): CustomType
	{
		return $this->type;
	}

	/**
	 * @return Location
	 */
	public function getPosition(): Location
	{
		return $this->position;
	}


    /**
     * @return NPCEventHandler
     */
    public function getHandler(): ?NPCEventHandler
    {
        return $this->handler;
    }



}