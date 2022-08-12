<?php

namespace battleoase\battlecore\player\provider;

use mysqli;

class PlayersProvider
{
    public static function get(mysqli $mysqli, string $xboxId): ?array {
        $query = $mysqli->query("SELECT * FROM players WHERE xuid='$xboxId'");
        return $query->fetch_assoc();
    }

    public static function register(mysqli $mysqli, string $xboxId, string $name): void {
        $mysqli->query("INSERT INTO players(xuid, name) VALUES ('$xboxId', '$name')");
    }

    public static function update(mysqli $mysqli, string $xboxId, array $update): void {
        $query = [];
        foreach($update as $key => $value) {
            $query[] = $key."='".$value."'";
        }
        $query = implode(", ", $query);
        $mysqli->query("UPDATE players SET ".$query." WHERE xuid='$xboxId'");
    }
}