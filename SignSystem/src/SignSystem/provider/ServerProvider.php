<?php

namespace SignSystem\provider;

class ServerProvider {

    private static array $usedServers = [];

    public static function addUsedServer(string $group, string $server) {
        if (!isset(self::$usedServers[$group])) self::$usedServers[$group] = [];
        if (!isset(self::$usedServers[$group][$server])) self::$usedServers[$group][$server] = true;
    }

    public static function removeUsedServer(string $group, string $server) {
        if (isset(self::$usedServers[$group])) {
            if (isset(self::$usedServers[$group][$server])) {
                unset(self::$usedServers[$group][$server]);
            }
        }
    }

    public static function isUsedServer(string $group, string $server): bool {
        return (isset(self::$usedServers[$group]) && isset(self::$usedServers[$group][$server]));
    }

    public static function getUsedServers(): array {
        return self::$usedServers;
    }
}