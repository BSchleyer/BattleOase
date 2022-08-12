<?php

namespace Cloud\event;

interface Cancellable {

    public function setCancelled(bool $v = true): void;

    public function isCancelled(): bool;
}