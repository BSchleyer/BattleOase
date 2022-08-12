<?php


namespace battleoase\battlecore\languageSystem;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\BattlePlayer;
use battleoase\battlecore\languageSystem\command\LanguageCommand;
use battleoase\battlecore\languageSystem\command\UpdateLanguageCommand;
use battleoase\battlecore\languageSystem\objects\Language;
use battleoase\battlecore\utils\BPlugin;
use battleoase\battlecore\utils\Internet;
use pocketmine\player\Player;

class LanguageSystem extends BPlugin {

    /** @var array<Language> $languages */
    public array $languages;

    public function __construct()
    {
        $this->getServer()->getCommandMap()->register("lang", new LanguageCommand());
        $this->getServer()->getCommandMap()->register("updatelanguage", new UpdateLanguageCommand());
        $this->loadLanguages();
    }

    public function loadLanguages(): void
    {
        $langs = ["de_DE", "en_US", "es_ES"];
        foreach ($langs as $lang) {
            $json = json_decode(Internet::getURL("https://raw.githubusercontent.com/BattleOase/language/master/" . $lang . ".json"), true);
            $this->languages[$lang] = new Language($json["name"], $json["localeCode"], $json["emoji"], $json["prefix"], $json["contributors"], $json["values"]);
        }
    }

    /**
     * @param $player
     * @param string $key
     * @param array|null $params
     * @return string
     */
    public function translate($player, string $key, ?array $params = []): string
    {
        if($player instanceof BattlePlayer) {
            $lang = $player->getNetworkLanguage();
            $locale = $lang;
        } else {
            $locale = "de_DE";
        }
        if(isset($this->languages[$locale]->getValues()[$key])) {
            $return = $this->languages[$locale]->getValues()[$key];
            $return = str_replace("{PREFIX}", $this->languages[$locale]->getPrefix(), str_replace("&", "§", $return));
            foreach ($params as $param => $index) {
                $return = str_replace("$param", $index, $return);
            }
            return $return;
        } else {
            return "$key";
        }
    }

    /**
     * @param $player
     * @param string $key
     * @param array|null $params
     * @return string
     */
    public static function translateFor($player, string $key, ?array $params = []): string
    {
        return BattleCore::getInstance()->languageSystem->translate($player, $key, $params);
    }
    
}