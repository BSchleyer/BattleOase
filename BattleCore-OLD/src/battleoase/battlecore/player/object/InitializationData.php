<?php

namespace battleoase\battlecore\player\object;

use battleoase\battlecore\network\customRaknet\WDPENetworkSession;
use ReflectionClass;

class InitializationData {
    public function __construct(
        public int $coins = -1,
        public string $language = "en_US",
        public string $lastSeen = "ja nixe da",
        public int $onlineTime = -1
    )
    {}

    public function toString(): string {
        $reflection = new ReflectionClass($this);
        $properties = [];
        foreach($reflection->getProperties() as $property) {
            if(!$property->isInitialized($this)) continue;
            $properties[$property->getName()] = $property->getValue($this);
        }
        return json_encode($properties);
    }

    public static function fromString(string $data): ?InitializationData {
        $data = @json_decode($data, true);
        if(empty($data)) return null;
        $self = new InitializationData();
        foreach($data as $key => $value) {
            $self->{$key} = $value;
        }
        return $self;
    }
}