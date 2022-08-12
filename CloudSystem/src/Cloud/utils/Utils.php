<?php

namespace Cloud\utils;

class Utils {

    const VERSION_POCKETMINE = 0;
    const VERSION_WATERDOGPE = 1;

    const PLUGIN_CLOUDBRIDGE_PM = 0;
    const PLUGIN_CLOUDBRIDGE_WD = 1;
    const PLUGIN_JOINHANDLER_WD = 2;

    private static array $versions = [
        0 => ["Name" => "PocketMine-MP", "Aliases" => ["pm", "pmmp"], "Url" => "https://github.com/pmmp/PocketMine-MP/releases/download/4.0.7/PocketMine-MP.phar"],
        1 => ["Name" => "WaterdogPE", "Aliases" => ["proxy", "wdpe", "wd"], "Url" => "https://jenkins.waterdog.dev/job/Waterdog/job/WaterdogPE/job/master/lastSuccessfulBuild/artifact/target/Waterdog.jar"]
    ];

    private static array $plugins = [
        0 => ["Name" => "CloudBridge-PM", "Aliases" => ["cb-pm"], "Version" => self::VERSION_POCKETMINE, "Url" => "https://github.com/PocketCloudSystem/CloudBridge-PM/releases/latest/download/CloudBridge-PM.phar"],
        1 => ["Name" => "CloudBridge-WD", "Aliases" => ["cb-wd"], "Version" => self::VERSION_WATERDOGPE, "Url" => "https://github.com/PocketCloudSystem/CloudBridge-WD/releases/latest/download/CloudBridge-WD-1.0-SNAPSHOT.jar"],
        2 => ["Name" => "JoinHandler", "Aliases" => ["jh", "jh-wd"], "Version" => self::VERSION_WATERDOGPE, "Url" => "https://github.com/PocketCloudSystem/JoinHandler/releases/latest/download/JoinHandler-1.0-SNAPSHOT.jar"]
    ];

    public static function downloadVersion(int $version) {
        if (isset(self::$versions[$version])) {
            $url = self::$versions[$version]["Url"];
            $path = "";
            if ($version == self::VERSION_POCKETMINE) {
                $path = CLOUD_PATH . "local/versions/pmmp/";
            } else if ($version == self::VERSION_WATERDOGPE) {
                $path = CLOUD_PATH . "local/versions/wdpe/";
            }
            file_put_contents($path . basename($url), file_get_contents($url, false, stream_context_create(["ssl" => ["verify_peer" => false, "verify_peer_name" => false]])));
        }
    }

    public static function hasDownloaded(int $version): bool {
        if (isset(self::$versions[$version])) {
            $baseName = basename(self::$versions[$version]["Url"]);
            if ($version == self::VERSION_POCKETMINE) {
                if (file_exists(CLOUD_PATH . "local/versions/pmmp/" . $baseName)) return true;
            } else if ($version == self::VERSION_WATERDOGPE) {
                if (file_exists(CLOUD_PATH . "local/versions/wdpe/" . $baseName)) return true;
            }
        }
        return false;
    }

    public static function downloadPlugin(int $plugin) {
        if (isset(self::$plugins[$plugin])) {
            $url = self::$plugins[$plugin]["Url"];
            $version = self::$plugins[$plugin]["Version"];
            $path = "";
            if ($version == self::VERSION_POCKETMINE) {
                $path = CLOUD_PATH . "local/plugins/pmmp/";
            } else if ($version == self::VERSION_WATERDOGPE) {
                $path = CLOUD_PATH . "local/plugins/wdpe/";
            }
            file_put_contents($path . basename($url), file_get_contents($url, false, stream_context_create(["ssl" => ["verify_peer" => false, "verify_peer_name" => false]])));
        }
    }

    public static function hasPluginDownloaded(int $plugin): bool {
        if (isset(self::$plugins[$plugin])) {
            $baseName = basename(self::$plugins[$plugin]["Url"]);
            $version = self::$plugins[$plugin]["Version"];
            if ($version == self::VERSION_POCKETMINE) {
                if (file_exists(CLOUD_PATH . "local/plugins/pmmp/" . $baseName)) return true;
            } else if ($version == self::VERSION_WATERDOGPE) {
                if (file_exists(CLOUD_PATH . "local/plugins/wdpe/" . $baseName)) return true;
            }
        }
        return false;
    }

    public static function getPlugins(): array {
        return self::$plugins;
    }

    public static function getVersions(): array {
        return self::$versions;
    }

    public static function getPluginInfo(int $plugin): array {
        $info = [];
        if (isset(self::$plugins[$plugin])) {
            $info["Name"] = self::$plugins[$plugin]["Name"];
            $info["Aliases"] = self::$plugins[$plugin]["Aliases"];
            $info["Url"] = self::$plugins[$plugin]["Url"];
            $info["Version"] = self::$plugins[$plugin]["Version"];
        }
        return $info;
    }

    public static function getVersionInfo(int $version): array {
        $info = [];
        if (isset(self::$versions[$version])) {
            $info["Name"] = self::$versions[$version]["Name"];
            $info["Aliases"] = self::$versions[$version]["Aliases"];
            $info["Url"] = self::$versions[$version]["Url"];
        }
        return $info;
    }

    public static function deleteDir($dirPath) {
        if (is_dir($dirPath)) {
            $folderHandle = opendir($dirPath);

            if (!$folderHandle) return;

            while($file = readdir($folderHandle)) {
                if (($file != ".") && ($file != "..")) {
                    if (is_dir($dirPath . "/" . $file))  {
                        self::deleteDir($dirPath . "/" . $file . "/");
                    } else {
                        unlink($dirPath . "/" . $file);
                    }
                }
            }

            closedir($folderHandle);
            rmdir($dirPath);
        }
    }

    public static function copyDir($src, $dst) {
        if (file_exists($src)) @mkdir($src);
        $dir = opendir($src);
        @mkdir($dst);
        while($file = readdir($dir)) {
            if (($file != ".") && ($file != "..")) {
                if (is_dir($src . "/" . $file))  {
                    self::copyDir($src . "/" . $file, $dst . "/" . $file);
                } else {
                    copy($src . "/" . $file, $dst . "/" . $file);
                }
            }
        }
        closedir($dir);
    }

    public static function copyFile($src, $dst) {
        if (is_file($src)) {
            copy($src, $dst);
        }
    }

    public static function isTmuxInstalled(): bool {
        if (PHP_OS_FAMILY == "Linux") {
            if (file_exists("/usr/bin/tmux")) return true;
        }
        return false;
    }

    public static function isScreenInstalled(): bool {
        if (PHP_OS_FAMILY == "Linux") {
            if (file_exists("/usr/bin/screen")) return true;
        }
        return false;
    }

    public static function getBinary(): string {
        $path = "php";
        if (file_exists(CLOUD_PATH . "bin/")) {
            if (file_exists(CLOUD_PATH . "bin/php/")) { //windows
                $path = CLOUD_PATH . "bin/php/php.exe";
            } else if (file_exists(CLOUD_PATH . "bin/php7/")) {
                if (file_exists(CLOUD_PATH . "bin/php7/php.exe")) {
                    $path = CLOUD_PATH . "bin/php7/php.exe";
                } else if (file_exists(CLOUD_PATH . "bin/php7/bin/")) {
                    if (file_exists(CLOUD_PATH . "bin/php7/bin/php")) {
                        $path = CLOUD_PATH . "bin/php7/bin/php";
                    }
                }
            }
        }
        return $path;
    }

    public static function kill(int $pid, bool $subprocesses = true): void {
        switch(PHP_OS_FAMILY) {
            case "Windows":
                exec("taskkill.exe /F " . ($subprocesses ? "/T " : "") . "/PID $pid > NUL 2> NUL");
                break;
            case "Linux":
            default:
            if($subprocesses) {
                $pid = -$pid;
            }

            if(function_exists("posix_kill")) {
                posix_kill($pid, 9);
            }else {
                exec("kill -9 $pid > /dev/null 2>&1");
            }
        }
    }

    public static function stringifyKeys(array $array): \Generator {
        foreach($array as $key => $value) {
            yield (string) $key => $value;
        }
    }
}