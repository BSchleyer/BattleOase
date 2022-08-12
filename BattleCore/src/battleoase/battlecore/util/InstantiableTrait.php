<?php

namespace battleoase\battlecore\util;

trait InstantiableTrait {

    /** @var static|null $instance */
    private static ?self $instance = null;

    /**
     * @return static
     */
    public static function getInstance(): static {
        if (self::$instance === null) self::$instance = new static;
        return self::$instance;
    }

}