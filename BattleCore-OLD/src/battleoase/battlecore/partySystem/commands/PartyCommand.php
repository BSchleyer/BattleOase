<?php

namespace battleoase\battlecore\partySystem\commands;

use battleoase\battlecore\partySystem\forms\DefaultPartyForm;
use battleoase\battlecore\partySystem\PartySystem;
use Exception;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class PartyCommand extends Command {

    public function __construct()
    {
        parent::__construct("party", "Default Party Command", "/party", []);
    }

    /**
     * @throws Exception
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) return;
        if (!PartySystem::getDatabase()->isInParty($sender->getName())){
            $sender->sendForm(new DefaultPartyForm());
        } else {
            $partyName = PartySystem::getDatabase()->getPlayer($sender->getName())["party_name"] ?? "";
            if (PartySystem::getDatabase()->getParty($partyName)["party_owner"] === $sender->getName()){
                //Send Party manage form
            } else {
                //Send party info form
            }
        }
    }
}