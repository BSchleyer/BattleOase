<?php

namespace Cloud\template;

use Cloud\server\ServerManager;
use Cloud\utils\Config;
use Cloud\utils\Reloadable;
use Cloud\utils\Utils;

class TemplateManager implements Reloadable {

    private static self $instance;
    /** @var Template[] */
    private array $templates = [];

    public function __construct() {
        self::$instance = $this;
    }

    public function loadTemplates() {
        foreach ($this->getTemplateConfig()->getAll() as $name => $data) {
            if (isset($data["Enabled"]) && isset($data["MinServers"]) && isset($data["MaxServers"]) && isset($data["MaxPlayers"]) && isset($data["AutoStart"]) && isset($data["Type"])) {
                if (boolval($data["Enabled"]) == true) {
                    $this->templates[$name] = new Template($name, $data["MinServers"], $data["MaxServers"], $data["MaxPlayers"], boolval($data["AutoStart"]), $data["Type"]);
                }
            }
        }
    }

    public function createTemplate(Template $template) {

        $cfg = $this->getTemplateConfig();
        $cfg->set($template->getName(), [
            "Enabled" => true,
            "MinServers" => $template->getMinServers(),
            "MaxServers" => $template->getMaxServers(),
            "MaxPlayers" => $template->getMaxPlayers(),
            "AutoStart" => $template->isAutoStart(),
            "Type" => $template->getType()
        ]);
        $cfg->save();

        if (!file_exists(CLOUD_PATH . "templates/" . $template->getName() . "/")) mkdir(CLOUD_PATH . "templates/" . $template->getName() . "/", 0777);
        if (!isset($this->templates[$template->getName()])) $this->templates[$template->getName()] = $template;

        if ($template->getType() == $template::TYPE_SERVER) {
            if (Utils::hasDownloaded(Utils::VERSION_POCKETMINE)) {
                Utils::copyFile(CLOUD_PATH . "local/versions/pmmp/PocketMine-MP.phar", $template->getPath() . "PocketMine-MP.phar");
            }
        } else if ($template->getType() == $template::TYPE_PROXY) {
            if (Utils::hasDownloaded(Utils::VERSION_WATERDOGPE)) {
                Utils::copyFile(CLOUD_PATH . "local/versions/wdpe/Waterdog.jar", $template->getPath() . "Waterdog.jar");
            }
        }
    }

    public function removeTemplate(Template $template) {
        $cfg = $this->getTemplateConfig();
        $cfg->remove($template->getName());
        $cfg->save();

        ServerManager::getInstance()->stopTemplate($template);
        if (file_exists(CLOUD_PATH . "templates/" . $template->getName() . "/")) Utils::deleteDir(CLOUD_PATH . "templates/" . $template->getName() . "/");
        if (isset($this->templates[$template->getName()])) unset($this->templates[$template->getName()]);
    }

    public function isTemplateExisting(string $name): bool {
        return $this->getTemplateConfig()->exists($name);
    }

    public function getTemplate(string $name): ?Template {
        foreach ($this->templates as $template) {
            if ($template->getName() == $name) return $template;
        }
        return null;
    }

    public function getTemplates(): array {
        return $this->templates;
    }

    public function getTemplateConfig(): Config {
        return new Config(CLOUD_PATH . "templates/templates.json", 1);
    }

    public function reload(): void {
        $this->templates = [];
        $this->loadTemplates();
    }

    public static function getInstance(): TemplateManager {
        return self::$instance;
    }
}