<?php


namespace battleoase\battlecore\groupSystem\api;


use battleoase\battlecore\groupSystem\GroupSystem;

class GroupAPI
{

	public function getPrefix($group)
	{
		return GroupSystem::$groups[$group]->getChatFormat();
	}

	public function getNameTag($group)
	{
		return GroupSystem::$groups[$group]->getNametag();
	}

}