<?php

namespace battleoase\battlecore\player\object;

use ReflectionClass;

class InitializationData {

    public function __construct(
        public string $name,
        public string $xuid,
        public int $coins,
        public int $onlinetime,
        public string $extra
    ) {
    }

    /**
     * @return string
     */
    public function toString(): string {
        $reflection = new ReflectionClass($this);
        $properties = [];
        foreach($reflection->getProperties() as $property) {
            if(!$property->isInitialized($this)) continue;
            $properties[$property->getName()] = $property->getValue($this);
        }
        return json_encode($properties);
    }

    /**
     * @param string $data
     * @return InitializationData|null
     */
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