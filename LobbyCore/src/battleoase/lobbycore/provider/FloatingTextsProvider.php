<?php

namespace battleoase\lobbycore\provider;

use battleoase\battlecore\pmmpExtensions\world\FloatingTextParticle;
use pocketmine\math\Vector3;

class FloatingTextsProvider {



    /** @var FloatingTextParticle[] */
    private static array $floatingTextParticles = [];

    public static function getFloatingTextParticle($key): ?FloatingTextParticle{
        return self::$floatingTextParticles[$key] ?? null;
    }

    public function init(): void{
        self::addFloatingTextParticle(new FloatingTextParticle(new Vector3(-42751.5, 50.45, -5891.5), "§b•§r● §r§lWelcome to §b§lBattleOase.NET §r●§b• §r\n§b•§r● §b§lCLOSED BETA §r●§b•"));
        self::addFloatingTextParticle(new FloatingTextParticle(new Vector3(-42749.5, 46, -5863.5), "§5§lEvent"));
    }

    public static function addFloatingTextParticle(FloatingTextParticle $floatingTextParticle, $key = null): void{
        self::$floatingTextParticles[$key ?? count(self::$floatingTextParticles)] = $floatingTextParticle;
    }

    /**
     * @return FloatingTextParticle[]
     */
    public static function getFloatingTextParticles(): array{
        return self::$floatingTextParticles;
    }
}