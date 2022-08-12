<?php

namespace Cloud\server\id;

use Cloud\template\Template;

class IdManager {

    private static self $instance;
    private array $ids = [];

    public function __construct() {
        self::$instance = $this;
    }

    public function initTemplate(Template $template) {
        if (!isset($this->ids[$template->getName()])) $this->ids[$template->getName()] = [];
    }

    public function getFreeId(Template $template): int {
        for($i = 1; $i < 90000; $i++) {
            if (isset($this->ids[$template->getName()])) {
                if (in_array($i, $this->ids[$template->getName()])) {
                    continue;
                } else {
                    return $i;
                }
            }
        }
        return 0;
    }

    public function addId(Template $template, int $id) {
        if (isset($this->ids[$template->getName()])) {
            if (!in_array($id, $this->ids[$template->getName()])) {
                $this->ids[$template->getName()][] = $id;
            }
        } else {
            $this->ids[$template->getName()][] = $id;
        }
    }

    public function removeId(Template $template, int $id) {
        if (isset($this->ids[$template->getName()])) {
            if (in_array($id, $this->ids[$template->getName()])) {
                unset($this->ids[$template->getName()][array_search($id, $this->ids[$template->getName()])]);
            }
        }
    }

    public function getIds(): array {
        return $this->ids;
    }

    public static function getInstance(): IdManager {
        return self::$instance;
    }
}