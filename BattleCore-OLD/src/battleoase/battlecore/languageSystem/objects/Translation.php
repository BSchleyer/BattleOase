<?php

namespace battleoase\battlecore\languageSystem\objects;

class Translation
{

    public function __construct(
        public string $key,
        public array $parameters = []
    )
    {}

    public static function make(string $key, array $parameters = []) {
        return new Translation($key, $parameters);
    }

}