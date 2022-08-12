<?php

namespace battleoase\battlecore\languageSystem\form;

use battleoase\battlecore\BattleCore;
use battleoase\battlecore\BattlePlayer;
use battleoase\battlecore\languageSystem\objects\Language;
use pocketmine\player\Player;
use pocketmine\Server;
use jojoe77777\FormAPI\SimpleForm;

class LanguageForm
{
    private array $buttons = [];

    public function __construct(Player $player)
    {
        $api = Server::getInstance()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (BattlePlayer $player, int $data = null){
            if($data === null) {
                return true;
            }
            switch ($data) {
                case $data:
                    $language = BattleCore::getInstance()->getLanguageSystem()->languages[$this->buttons[$data]];
                    if($language instanceof Language) {
                        $player->setInfo("lang", $language->getLocale());
                        $player->sendMessage(BattleCore::getInstance()->getLanguageSystem()->translate($player, "LanguageSystem.change"));
                    } else {
                        $player->sendMessage("§cWe couldn't load the language");
                    }
                    break;
            }
        });
        $form->setTitle("§e§lLanguage");
        $this->buttons = [];
        foreach (BattleCore::getInstance()->getLanguageSystem()->languages as $language) {
            if($language instanceof Language) {
                $this->buttons[] = $language->getLocale();
                $form->addButton($language->getName() . " §7| §e" . count($language->getValues()) . "\n" . "Translations",0,  "textures/ui/language_glyph_color");
            }
        }
        $form->sendToPlayer($player);

        return $form;
    }
}