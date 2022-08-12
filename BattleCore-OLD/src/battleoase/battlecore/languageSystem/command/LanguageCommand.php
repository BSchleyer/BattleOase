<?php


namespace battleoase\battlecore\languageSystem\command;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\languageSystem\form\LanguageForm;
use battleoase\battlecore\languageSystem\objects\Language;
use battleoase\battlecore\npcSystem\caches\TypeCache;
use battleoase\battlecore\npcSystem\classes\NPCBuilder;
use battleoase\battlecore\npcSystem\handler\preset\CommandExecutionHandler;
use battleoase\battlecore\utils\Internet;
use Frago9876543210\EasyForms\elements\Dropdown;
use Frago9876543210\EasyForms\forms\CustomForm;
use Frago9876543210\EasyForms\forms\CustomFormResponse;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\player\Player;
use pocketmine\Server;

class LanguageCommand extends Command
{
    public function __construct()
    {
        parent::__construct("lang", "Lang Command", "/lang", ["language"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender instanceof Player){
            new LanguageForm($sender);
        }
    }

}