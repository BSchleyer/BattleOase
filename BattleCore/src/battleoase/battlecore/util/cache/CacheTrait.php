<?php

namespace battleoase\battlecore\utils\cache;

trait CacheTrait {
    private ?Cache $cache = null;

    public function resetCache(): void{
        $this->checkCache();
        $this->cache->setAll([]);
    }

    private function checkCache(): void{
        if($this->cache !== null) return;
        $this->cache = new Cache();
    }

    public function getCache(): Cache{
        $this->checkCache();
        return $this->cache;
    }
}