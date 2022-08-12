<?php

use Cloud\Cloud;

require 'vendor/autoload.php';

if (is_phar()) {
    define("CLOUD_PATH", str_replace("phar://", "", dirname(__DIR__, 3) . DIRECTORY_SEPARATOR));
} else {
    define("CLOUD_PATH", dirname(__DIR__, 2) . DIRECTORY_SEPARATOR);
}

if (!file_exists(CLOUD_PATH . "local/")) mkdir(CLOUD_PATH . "local/", 0777);
if (!file_exists(CLOUD_PATH . "local/notify/")) mkdir(CLOUD_PATH . "local/notify/", 0777);
if (!file_exists(CLOUD_PATH . "local/plugins/")) mkdir(CLOUD_PATH . "local/plugins/", 0777);
if (!file_exists(CLOUD_PATH . "local/plugins/pmmp/")) mkdir(CLOUD_PATH . "local/plugins/pmmp/", 0777);
if (!file_exists(CLOUD_PATH . "local/plugins/wdpe/")) mkdir(CLOUD_PATH . "local/plugins/wdpe/", 0777);
if (!file_exists(CLOUD_PATH . "local/versions/")) mkdir(CLOUD_PATH . "local/versions/", 0777);
if (!file_exists(CLOUD_PATH . "local/versions/pmmp/")) mkdir(CLOUD_PATH . "local/versions/pmmp/", 0777);
if (!file_exists(CLOUD_PATH . "local/versions/wdpe/")) mkdir(CLOUD_PATH . "local/versions/wdpe/", 0777);
if (!file_exists(CLOUD_PATH . "templates/")) mkdir(CLOUD_PATH . "templates/", 0777);
if (!file_exists(CLOUD_PATH . "temp/")) mkdir(CLOUD_PATH . "temp/", 0777);
if (!file_exists(CLOUD_PATH . "temp/servers/")) mkdir(CLOUD_PATH . "temp/servers/", 0777);
if (!file_exists(CLOUD_PATH . "local/cloud.log")) file_put_contents(CLOUD_PATH . "local/cloud.log", "");

new Cloud(CLOUD_PATH);

function is_phar(): bool {
    return boolval(Phar::running());
}
