<?php


namespace battleoase\battlecore\utils;

use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;

class Math
{
	/**
	 * @param Vector3 $base
	 * @param Vector3 $target
	 * @param float|int $steps
	 * @return int
	 */
	public static function calculateDirection(Vector3 $base, Vector3 $target, float $steps = 1): int
	{
		for ($i = 0; $i < 3; $i++) {
			if ($base->getSide($i, $steps)->floor()->equals($target->floor())) return $i;
		}
		return 0;
	}

	/**
	 * @param Vector3 $pos1
	 * @param Vector3 $pos2
	 * @return AxisAlignedBB
	 */
	public static function createAxisAlignedBB(Vector3 $pos1, Vector3 $pos2): AxisAlignedBB
	{
		return new AxisAlignedBB(
			$pos1->x < $pos2->x ? $pos1->x:$pos2->x,
			$pos1->y < $pos2->y ? $pos1->y:$pos2->y,
			$pos1->z < $pos2->z ? $pos1->z:$pos2->z,
			$pos1->x > $pos2->x ? $pos1->x:$pos2->x,
			$pos1->y > $pos2->y ? $pos1->y:$pos2->y,
			$pos1->z > $pos2->z ? $pos1->z:$pos2->z,
		);
	}


	/**
	 * @param Entity $entity
	 * @return Block
	 */
	public static function getFrontBlock(Entity $entity): Block
	{
		switch ($entity->getDirectionVector()) {
			case 2:
				return $entity->getWorld()->getBlock(new Vector3($entity->x - 1, $entity->y, $entity->z));
			case 0:
				return $entity->getWorld()->getBlock(new Vector3($entity->x + 1, $entity->y, $entity->z));
			case 3:
				return $entity->getWorld()->getBlock(new Vector3($entity->x, $entity->y, $entity->z - 1));
			case 1:
				return $entity->getWorld()->getBlock(new Vector3($entity->x, $entity->y, $entity->z + 1));
			default:
				return $entity->getWorld()->getBlock(new Vector3($entity->x, $entity->y, $entity->z));
		}
	}


	/**
	 * @param string $vector
	 * @param string $delimiter
	 * @return Vector3
	 */
	public static function stringVectorToVector3(string $vector, string $delimiter = ","): Vector3
	{
		$vector3 = explode($delimiter, $vector);
		return new Vector3((int)$vector3[0], (int)$vector3[1], (int)$vector3[2]);
	}

	/**
	 * Convert vector to string
	 *
	 * @param Vector3
	 * @return string
	 */
	public static function vector3ToString(Vector3 $vector3): string
	{
		return $vector3->x . ":" . $vector3->y . ":" . $vector3->z;
	}
}