<?php

namespace Cloud\utils;

class Config {

    const PROPERTIES = 0;
    const JSON = 1;
    const YAML = 2;

    private array $parsedContent = [];
    private string $path;
    private int $type;

    public function __construct(string $path, int $type = 0) {
        $this->path = $path;
        $this->type = $type;
        if (!file_exists($path)) @fopen($path, "w");
        $this->load($path);
    }

    public function getNested(string $key, $default = null): mixed {
        $values = explode(".", $key);

        $array = $this->parsedContent;
        foreach ($values as $value) {
            if (isset($array[$value])) {
                $array = $array[$value];
            } else {
                return $default;
            }
        }
        return $array;
    }

    public function setNested(string $key, $value) {
        $vars = explode(".", $key);
        $base = array_shift($vars);

        if(!isset($this->parsedContent[$base])){
            $this->parsedContent[$base] = [];
        }

        $base =& $this->parsedContent[$base];

        while(count($vars) > 0){
            $baseKey = array_shift($vars);
            if(!isset($base[$baseKey])){
                $base[$baseKey] = [];
            }
            $base =& $base[$baseKey];
        }

        $base = $value;
    }

    public function get(string $key, $default = null): mixed {
        return $this->parsedContent[$key] ?? $default;
    }

    public function set(string $key, $value) {
        $this->parsedContent[$key] = $value;
    }

    public function exists(string $key): bool {
        return isset($this->parsedContent[$key]);
    }

    public function existsNested(string $key): bool {
        $vars = explode(".", $key);
        $array = $this->parsedContent;
        foreach ($vars as $var) {
            if (!isset($array[$var])) return false;
            if (!is_array($array[$var])) return true;
            $array = $array[$var];
        }
        return true;
    }

    public function remove(string $key): bool {
        if (!$this->exists($key)) return false;
        unset($this->parsedContent[$key]);
        return true;
    }

    public function removeNested(string $key): bool {
        if (!$this->existsNested($key)) return false;

        $vars = explode(".", $key);

        $currentNode =& $this->parsedContent;
        while(count($vars) > 0){
            $nodeName = array_shift($vars);
            if(isset($currentNode[$nodeName])){
                if(count($vars) === 0){
                    unset($currentNode[$nodeName]);
                }elseif(is_array($currentNode[$nodeName])){
                    $currentNode =& $currentNode[$nodeName];
                }
            }else{
                return false;
            }
        }
        return true;
    }

    public function getAll(bool $keys = false): array {
        return ($keys ? array_keys($this->parsedContent) : $this->parsedContent);
    }

    public function save() {
        switch ($this->type) {
            case 0:
                file_put_contents($this->path, $this->writeProperties($this->parsedContent));
                break;
            case 1:
                file_put_contents($this->path, json_encode($this->parsedContent, JSON_PRETTY_PRINT));
                break;
            case 2:
                file_put_contents($this->path, @yaml_emit($this->parsedContent));
                break;
        }
    }

    public function reload() {
        $this->parsedContent = [];
        $this->load($this->path);
    }

    public function load(string $path) {
        $this->parsedContent = match ($this->type) {
            0 => $this->parseProperties(@file_get_contents($path)) ?? [],
            1 => @json_decode(@file_get_contents($path), true) ?? [],
            2 => @yaml_parse(@file_get_contents($path)) ?? [],
        };
    }

    private function writeProperties(array $config): string{
        $content = "";
        foreach(Utils::stringifyKeys($config) as $k => $v){
            if(is_bool($v)){
                $v = $v ? "on" : "off";
            }
            $content .= $k . "=" . $v . "\r\n";
        }

        return $content;
    }

    private function parseProperties(string $content) : array{
        $result = [];
        if(preg_match_all('/^\s*([a-zA-Z0-9\-_\.]+)[ \t]*=([^\r\n]*)/um', $content, $matches) > 0) {
            foreach($matches[1] as $i => $k) {
                $v = trim($matches[2][$i]);
                switch(strtolower($v)) {
                    case "on":
                    case "true":
                    case "yes":
                        $v = true;
                        break;
                    case "off":
                    case "false":
                    case "no":
                        $v = false;
                        break;
                    default:
                        $v = match($v) {
                            (string) ((int) $v) => (int) $v,
                            (string) ((float) $v) => (float) $v,
                            default => $v,
                        };
                        break;
                }
                $result[(string) $k] = $v;
            }
        }
        return $result;
    }
}