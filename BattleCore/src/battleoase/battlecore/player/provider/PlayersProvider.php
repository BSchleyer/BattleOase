<?php

namespace battleoase\battlecore\player\provider;

use battleoase\battlecore\BattleCore;
use mysqli;

class PlayersProvider {

    public static function init(): void {
        BattleCore::getInstance()->getConnection()->query("CREATE TABLE `Core`.`players` ( `id` INT NOT NULL AUTO_INCREMENT , `name` VARCHAR(16) NOT NULL , `xboxid` VARCHAR(16) NOT NULL , `coins` INT NOT NULL DEFAULT '1000' , `onlinetime` INT NOT NULL DEFAULT '0' , `extra` LONGTEXT NULL DEFAULT '[]' , PRIMARY KEY (`id`)) ENGINE = InnoDB;");
    }

    public static function get(mysqli $mysqli, string $xboxId): ?array {
        $query = $mysqli->query("SELECT * FROM players WHERE xboxid='$xboxId'");
        return $query->fetch_assoc();
    }

    public static function register(mysqli $mysqli, string $xboxId, string $name): void {
        $mysqli->query("INSERT INTO players(xboxid, name) VALUES ('$xboxId', '$name')");
    }

    public static function update(mysqli $mysqli, string $xboxId, array $update): void {
        $query = [];
        foreach($update as $key => $value) {
            $query[] = $key."='".$value."'";
        }
        $query = implode(", ", $query);
        $mysqli->query("UPDATE players SET ".$query." WHERE xboxid='$xboxId'");
    }
}