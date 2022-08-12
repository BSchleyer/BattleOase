<?php


namespace battleoase\battlecore\groupSystem\objects;


use pocketmine\Server;
use pocketmine\utils\MainLogger;
use battleoase\battlecore\groupSystem\GroupSystem;

class Group
{

	public function __construct(protected string $name, protected string $nametag, protected string $chatformat, protected string $color, protected array $permissions, protected array $inheritance) {}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getChatFormat(): string
	{
		return $this->chatformat;
	}

	/**
	 * @return array
	 */
	public function getInheritance(): array
	{
		return $this->inheritance;
	}

	/**
	 * @return string
	 */
	public function getNametag(): string
	{
		return $this->nametag;
	}

	/**
	 * @return array
	 */
	public function getPermissions(): array
	{
		return $this->permissions;
	}

	/**
	 * @return string
	 */
	public function getDefaultGroup(): string {
		return GroupSystem::DEFAULT_GROUP;
	}

	/**
	 * @return string
	 */
	public function getColor(): string
	{
		return $this->color;
	}

}