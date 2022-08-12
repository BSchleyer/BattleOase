<?php

namespace Cloud\utils;

use Cloud\Cloud;

class CloudLogger {

    private static self $instance;
    private CloudColors $cloudColors;
    private string $path = CLOUD_PATH . "local/cloud.log";
    /** @var false|resource */
    private $resource;

    public static function getInstance(): self {
        return self::$instance;
    }

    public function __construct() {
        self::$instance = $this;
        $this->cloudColors = new CloudColors();

        $this->resource = fopen($this->path, "ab");
    }

    public function info(string $message) {
        $this->send("§bINFORMATION", $message);
    }

    public function warning(string $message) {
        $this->send("§cWARNING", $message);
    }

    public function error(string  $message) {
        $this->send("§4ERROR", $message);
    }

    public function message(string $message) {
        $this->send("§6MESSAGE", $message);
    }

    public function debug(string $message, bool $force = false) {
        if (!Cloud::getInstance()->isDebugModeEnabled() && !$force) $this->send("§eDEBUG", $message);
    }

    private function send(string $prefix, string $message) {
        $format = "§8[§e" . date("H:i:s") . "§8] §8[" . $prefix . "§8] §r" . $message . "§r" . PHP_EOL;
        $this->writeFile(CloudColors::getInstance()->toColoredString($format, false));
        echo "\r" . CloudColors::getInstance()->toColoredString($format);
    }

    public function writeFile(string $message = "") {
        fwrite($this->resource, $message);
    }

    public function close() {
        fclose($this->resource);
    }

    public function getCloudColors(): CloudColors {
        return $this->cloudColors;
    }
}