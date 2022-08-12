<?php

namespace battleoase\battlecore\npcSystem\handler\preset;

use battleoase\battlecore\npcSystem\handler\NPCEventHandler;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\player\Player;

class TitleHandler extends NPCEventHandler
{

	private $title = "";
	private $subtitle = "";

	private $fadeIn = -1;
	private $stay = -1;
	private $fadeOut = -1;

	public function onHit(Entity &$entity, EntityDamageByEntityEvent &$event): bool
	{
		$player = $event->getDamager();
		if ($player instanceof Player) {
			$player->sendTitle($this->getTitle(), $this->getSubtitle(), $this->getFadeIn(), $this->getStay(), $this->getFadeOut());
		}
		return false;
	}

	/**
	 * @return string
	 */
	public function getSubtitle(): string
	{
		return $this->subtitle;
	}

	/**
	 * @return string
	 */
	public function getTitle(): string
	{
		return $this->title;
	}

	/**
	 * @param string $subtitle
	 */
	public function setSubtitle(string $subtitle): void
	{
		$this->subtitle = $subtitle;
	}

	/**
	 * @param string $title
	 */
	public function setTitle(string $title): void
	{
		$this->title = $title;
	}

	/**
	 * @return int
	 */
	public function getFadeIn(): int
	{
		return $this->fadeIn;
	}

	/**
	 * @return int
	 */
	public function getFadeOut(): int
	{
		return $this->fadeOut;
	}

	/**
	 * @return int
	 */
	public function getStay(): int
	{
		return $this->stay;
	}

	/**
	 * @param int $fadeIn
	 */
	public function setFadeIn(int $fadeIn): void
	{
		$this->fadeIn = $fadeIn;
	}

	/**
	 * @param int $fadeOut
	 */
	public function setFadeOut(int $fadeOut): void
	{
		$this->fadeOut = $fadeOut;
	}

	/**
	 * @param int $stay
	 */
	public function setStay(int $stay): void
	{
		$this->stay = $stay;
	}

    public function getName(): string {
        return "title";
    }
}