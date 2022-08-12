<?php


namespace battleoase\battlecore\languageSystem\command;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\languageSystem\LanguageSystem;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class UpdateLanguageCommand extends Command
{

    public function __construct()
    {
        parent::__construct("updatelanguage", "Update the Language Cache");
        $this->setPermission("admin");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender->hasPermission("admin")){
            unset(BattleCore::getInstance()->getLanguageSystem()->languages);
            BattleCore::getInstance()->getLanguageSystem()->loadLanguages();
            $sender->sendMessage(BattleCore::getPrefix() . "§7Du hast erfolgreich die Language Cache geleert! Die Language-Data wurde neu aufgesetzt!");
        }
    }
}