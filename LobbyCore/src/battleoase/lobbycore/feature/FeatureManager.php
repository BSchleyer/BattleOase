<?php

namespace battleoase\lobbycore\feature;

use battleoase\battlecore\BattlePlayer;
use battleoase\lobbycore\LobbyCore;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use ReflectionClass;
use ReflectionException;

class FeatureManager
{
    use SingletonTrait;

    /** @var Feature[] */
    private array $features = [];
    /** @var Feature[] */
    private array $updatingFeatures = [];

    public static function get(string $feature): ?Feature
    {
        return FeatureManager::getInstance()->getFeatures()[$feature] ?? null;
    }

    /**
     * @throws ReflectionException
     */
    public function __construct()
    {
        self::$instance = $this;
        foreach (scandir(__DIR__) as $directory) {
            if (!is_dir(__DIR__ . DIRECTORY_SEPARATOR . $directory) || in_array($directory, [".", ".."])) continue;
            foreach (scandir(__DIR__ . DIRECTORY_SEPARATOR . $directory) as $file) {
                if (!str_ends_with($file, ".php") || in_array($file, [".", ".."])) continue;
                $baseDirectory = LobbyCore::getInstance()->getFile() . "src/";
                $file = str_replace("/", "\\", str_replace([$baseDirectory, ".php"], "", __DIR__ . "/" . $directory . "/" . $file));
                $reflectionClass = new ReflectionClass($file);
                $class = new ($reflectionClass->getName());
                if ($class instanceof Feature) {
                    $this->registerFeature($class);
                }
            }
        }

        LobbyCore::getInstance()->getScheduler()->scheduleRepeatingTask(
            new class() extends Task {
                public function onRun(): void
                {
                    foreach (FeatureManager::getInstance()->getUpdatingFeatures() as $feature) {
                        $update = $feature->onUpdate();
                        if (!$update) FeatureManager::getInstance()->unregisterUpdatingFeature($feature);
                    }
                }
            }, 1
        );
    }

    /**
     * @throws ReflectionException
     */
    public function registerFeature(Feature $feature): void
    {
        $ref = new ReflectionClass($feature::class);
        $feature->name = $ref->getShortName();

        $this->features[$feature::class] = $feature;

        $this->scheduleUpdate($feature);

        $feature->onLoad();
        Server::getInstance()->getPluginManager()->registerEvents($feature, LobbyCore::getInstance());
    }

    public function getFeatures(): array
    {
        return $this->features;
    }

    public function scheduleUpdate(Feature $feature): void
    {
        $this->updatingFeatures[$feature::class] = $feature;
    }

    public function getUpdatingFeatures(): array
    {
        return $this->updatingFeatures;
    }

    public function unregisterUpdatingFeature(Feature $feature): void
    {
        unset($this->updatingFeatures[$feature::class]);
    }
}