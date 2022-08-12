<?php

namespace Cloud\command;

abstract class Command {

    private string $name;
    private string $description;
    private string $usage;
    private array $aliases = [];

    public function __construct(string $name, $description = "", $usage = "", $aliases = []) {
        $this->name = $name;
        $this->description = $description;
        $this->usage = $usage;
        $this->aliases = $aliases;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getUsage(): string {
        return $this->usage;
    }

    public function getAliases(): array {
        return $this->aliases;
    }

    abstract public function execute(array $args): bool;
}