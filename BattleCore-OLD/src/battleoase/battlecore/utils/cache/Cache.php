<?php

namespace battleoase\battlecore\utils\cache;

class Cache
{
    private array $cache = [];

    public function set(string|int $key, mixed $value): void{
        $this->cache[$key] = $value;
    }

    public function get(string|int $key, mixed $default = null): mixed{
        return $this->cache[$key] ?? $default;
    }

    public function exists(string|int $key): bool{
        return isset($this->cache[$key]);
    }

    public function remove(string|int $key): void{
        unset($this->cache[$key]);
    }

    public function getAll(): array{
        return $this->cache;
    }

    public function setAll(array $array): void{
        $this->cache = $array;
    }
}