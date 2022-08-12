<?php

namespace battleoase\battlecore\pmmpExtensions;

use pocketmine\utils\TextFormat;

class text {

    /**
     * Function colorize
     * @param string $string
     * @return string
     */
    public static function colorize(string $string): string{
        $end = "";
        $colors = ["§1","§2","§3","§4","§5","§6","§7","§8","§9","§a","§b","§c","§d","§e","§f","§g"];
        $str = TextFormat::clean($string);
        foreach (str_split($str) as $char) {
            $selectedColor = $colors[mt_rand(0, (count($colors) -1))];
            $end .= $selectedColor.$char;
        }
        return $end;
    }

    /**
     * Function boolToString
     * @param bool $value
     * @return string
     */
    public static function boolToString(bool $value): string{
        return ($value ? "§atrue" : "§cfalse");
    }

}