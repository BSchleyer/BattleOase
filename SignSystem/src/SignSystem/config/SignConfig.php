<?php

namespace SignSystem\config;

use pocketmine\utils\Config;

class SignConfig extends Config {

    public function __construct(string $file, int $type = Config::DETECT, array $default = []) {
        parent::__construct($file, $type, $default);
        if($this->exists("reload")) return;
        $this->setAll([
            "reload" => 3,
            "SignFormat" => [
                "online" => [
                    "0" => ["%server_name%", "Online", "%online_count%/%max_player%"]
                ]
            ]
        ]);
        $this->save();
    }

    public function set($k, $v = true): void {
        parent::set($k, $v);
        $this->save();
    }

    public function getReload(): int {
        return $this->get("reload", 3);
    }

    public function setReload(int $interval = 3): void {
        $this->set("reload", $interval);
    }

    public function getFormat(string $name): array {
        $format = [];
        if ($this->getNested("SignFormat." . $name) !== null) {
            if (is_array($this->getNested("SignFormat." . $name))) {
                $format = $this->getNested("SignFormat." . $name);
            }
        }
        return $format;
    }

}