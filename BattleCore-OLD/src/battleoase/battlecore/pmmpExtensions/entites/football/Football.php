<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 23.05.2021
 * Time: 23:45
 */


namespace battleoase\battlecore\pmmpExtensions\entites\football;

use battleoase\battlecore\BattleCore;
use pocketmine\entity\Entity;
use pocketmine\entity\Skin;
use pocketmine\player\Player;

class Football
{


    public static function spawnFootball(Player $player) : void {
        $image = imagecreatefrompng(BattleCore::getInstance()->getDataFolder()."football.png");
        $bytes = "";
        $l = (int) @getimagesize(BattleCore::getInstance()->getDataFolder()."football.png")[1];
        for ($y = 0; $y < $l; $y++) {
            for ($x = 0; $x < 64; $x++) {
                $rgba = @imagecolorat($image, $x, $y);
                $a = ((~((int)($rgba >> 24))) << 1) & 0xff;
                $r = ($rgba >> 16) & 0xff;
                $g = ($rgba >> 8) & 0xff;
                $b = $rgba & 0xff;
                $bytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }
        @imagedestroy($image);
        $footballEntity = new footballEntity($player->getLocation(), new Skin("Football", $bytes, "", "geometry.football", file_get_contents(BattleCore::getInstance()->getDataFolder()."football.json")));
        $footballEntity->sendSkin();
        $footballEntity->spawnToAll();
    }







}