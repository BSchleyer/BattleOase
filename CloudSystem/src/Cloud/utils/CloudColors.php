<?php

namespace Cloud\utils;

class CloudColors {

    private static self $instance;

    private array $colors = [];

    public function __construct() {
        self::$instance = $this;
        $this->registerColor("0", "\x1b[38;5;16m");
        $this->registerColor("f", "\x1b[38;5;231m");
        $this->registerColor("8", "\x1b[38;5;59m");
        $this->registerColor("7", "\x1b[38;5;145m");
        $this->registerColor("9", "\x1b[38;5;63m");
        $this->registerColor("1", "\x1b[38;5;19m");
        $this->registerColor("3", "\x1b[38;5;37m");
        $this->registerColor("b", "\x1b[38;5;87m");
        $this->registerColor("4", "\x1b[38;5;124m");
        $this->registerColor("c", "\x1b[38;5;203m");
        $this->registerColor("2", "\x1b[38;5;34m");
        $this->registerColor("a", "\x1b[38;5;83m");
        $this->registerColor("5", "\x1b[38;5;127m");
        $this->registerColor("d", "\x1b[38;5;207m");
        $this->registerColor("e", "\x1b[38;5;227m");
        $this->registerColor("6", "\x1b[38;5;214m");
        $this->registerColor("r", "\x1b[m");
    }

    public function registerColor(string $colorCode, string $color) {
        if (!isset($this->colors["§" . $colorCode])) {
            $this->colors["§" . $colorCode] = $color;
        }
    }

    public function toColoredString(string $string, bool $formatting = true): string {
        foreach ($this->colors as $colorCode => $color) {
            $string = str_replace($colorCode, ($formatting ? $color : ""), $string);
        }
        return $string;
    }

    public function getColors(): array {
        return $this->colors;
    }

    public static function getInstance(): CloudColors {
        return self::$instance;
    }
}