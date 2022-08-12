<?php

namespace battleoase\battlecore\npcSystem\classes;
use pocketmine\entity\Skin;

class CustomType
{

	/**
	 * @var string
	 */
	private $name;
	/**
	 * @var string
	 */
	private $geometry = "";

	/**
	 * @var string
	 */
	private $imageData = "";

	/**
	 * @var string
	 */
	private $geometryName = "";

	/**
	 * @return mixed
	 */
	public function getGeometry()
	{
		return $this->geometry;
	}

	/**
	 * @return mixed
	 */
	public function getImageData()
	{
		return $this->imageData;
	}

	/**
	 * @param string $geometry
	 */
	public function setGeometry(string $geometry): void
	{
		$this->geometry = $geometry;
	}

	/**
	 * @param string $imageData
	 */
	public function setImageData(string $imageData): void
	{
		$this->imageData = $imageData;
	}

	/**
	 * @param string $name
	 */
	public function setName(string $name): void
	{
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	public function toGeometryLessSkin(): ?Skin{
		return new Skin(uniqid(), $this->getImageData());
	}

	/**
	 * @return string
	 */
	public function getGeometryName(): string
	{
		return $this->geometryName;
	}

	/**
	 * @param string $geometryName
	 */
	public function setGeometryName(string $geometryName): void
	{
		$this->geometryName = $geometryName;
	}
}