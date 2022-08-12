<?php

namespace Cloud\event;

use Cloud\event\cloud\CloudStartedEvent;

class EventManager {

    private static self $instance;
    /** @var Listener[] */
    private array $listeners = [];
    /** @var Event[] */
    private array $events = [];

    public function __construct() {
        self::$instance = $this;
        $this->registerEvent(new CloudStartedEvent());
    }

    public function registerEvent(Event $event) {
        if (!isset($this->events[$event->getName()])) $this->events[$event->getName()] = $event;
    }

    public function registerListener(Listener $listener) {
        $this->listeners[] = $listener;
    }

    public function callEvent(Event $event) {
        foreach ($this->listeners as $listener) {
            foreach ((new \ReflectionClass($listener))->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                if ($method->isPublic() && !$method->isAbstract()) {
                    if ($method->getNumberOfParameters() == 1) {
                        if (is_subclass_of(strval($method->getParameters()[0]->getType()), Event::class)) {
                            if ($this->isEventRegistered($event)) {
                                $method->getClosure($listener)($event);
                            }
                        }
                    }
                }
            }
        }
    }

    public function isEventRegistered(Event|string $event): bool {
        $name = $event instanceof Event ? $event->getName() : $event;
        return isset($this->events[$name]);
    }

    public static function getInstance(): EventManager {
        return self::$instance;
    }
}