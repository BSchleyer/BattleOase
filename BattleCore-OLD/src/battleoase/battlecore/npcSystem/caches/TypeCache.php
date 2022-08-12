<?php

namespace battleoase\battlecore\npcSystem\caches;

use battleoase\battlecore\BattleCore;
use battleoase\battlecore\npcSystem\classes\CustomType;

class TypeCache
{
	/**
	 * @var CustomType[]
	 */
	private static $types = [];

	public static function add(CustomType $type): void
	{
		if (!self::exists($type->getName())) {
			self::$types[$type->getName()] = $type;
		}
	}

	public static function exists(string $typeName): bool
	{
		return array_key_exists($typeName, self::$types);
	}

	public static function get(string $typeName): ?CustomType
	{
		if (self::exists($typeName)) {
            return self::$types[$typeName];
        } else {
            $result = BattleCore::getInstance()->getMysqlConnection()->query("SELECT * FROM Stats.Skins WHERE player_name = '{$typeName}'");
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $type = new CustomType();
                    $type->setGeometry($row["geometry_data"]);
                    $type->setImageData(zlib_decode(base64_decode($row["skin_data"])));
                    $type->setGeometryName($row["geometry_name"]);
                    $type->setName($row["geometry_name"]);
                    return $type;
                }
            }
        }
		return null;
	}

	public static function getAll(): array
	{
		return self::$types;
	}
}