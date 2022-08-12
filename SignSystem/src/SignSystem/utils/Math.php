<?php

namespace SignSystem\utils;

use pocketmine\math\Vector3;

class Math {

    /**
     * Convert string to vector3
     *
     * @param string $vector
     * @param string $delimiter
     * @return Vector3
     */
    public static function stringVectorToVector3(string $vector, string $delimiter = ":"): Vector3 {
        $vector3 = explode($delimiter, $vector);
        return new Vector3((int)$vector3[0], (int)$vector3[1], (int)$vector3[2]);
    }

    /**
     * Convert vector3 to string
     *
     * @param Vector3
     * @return string
     */
    public static function vector3ToString(Vector3 $vector3): string {
        return (int)$vector3->x . ":" . (int)$vector3->y . ":" . (int)$vector3->z;
    }
}