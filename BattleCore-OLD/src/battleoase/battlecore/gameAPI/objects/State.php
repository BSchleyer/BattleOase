<?php

namespace battleoase\battlecore\gameAPI\objects;

use pocketmine\utils\RegistryTrait;

/**
 * @method static STATE WAITING()
 * @method static STATE STARTING()
 * @method static STATE INGAME()
 * @method static STATE ENDING()
 */

final class State {
    use RegistryTrait;

    protected static function setup(): void {
        self::_registryRegister("waiting", new State("WAITING"));
        self::_registryRegister("starting", new State("STARTING"));
        self::_registryRegister("ingame", new State("INGAME"));
        self::_registryRegister("ending", new State("ENDING"));
    }

    private string $name;

    public function __construct(string $name) {
        $this->name = $name;
    }

    public function getName(): string {
        return $this->name;
    }
}