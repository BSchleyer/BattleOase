<?php

namespace battleoase\battlecore\npcSystem\classes;
class AssignableTag
{

	private $name = "";

	private $displayLayout = "";

	/**
	 * @return string
	 */
	public function getDisplayLayout(): string
	{
		return $this->displayLayout;
	}

	/**
	 * @param string $displayLayout
	 */
	public function setDisplayLayout(string $displayLayout): void
	{
		$this->displayLayout = $displayLayout;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName(string $name): void
	{
		$this->name = $name;
	}
}