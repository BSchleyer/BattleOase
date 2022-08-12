<?php


namespace battleoase\battlecore\verificationSystem\utils;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\BattlePlayer;
use Frago9876543210\EasyForms\elements\Button;
use Frago9876543210\EasyForms\forms\MenuForm;
use pocketmine\player\Player;

class VerificationUtils
{

    /**
     * @param Player $player
     * @param string $verificationKey
     * @param bool $verificationStatus
     * @return void
     */
    public static function sendVerificationUi(Player $player, string $verificationKey, bool $verificationStatus = false): void
    {
        if ($player instanceof BattlePlayer) {
            if ($verificationStatus == false) {
                $player->sendForm(new MenuForm(
                    "§8• §aVerification §8•§r§f", BattleCore::getInstance()->getLanguageSystem()->translate($player, "verification.status.false"),
                    [
                        new Button(BattleCore::getInstance()->getLanguageSystem()->translate($player, "verification.player.verificationKey", [
                            "{KEY}" => $verificationKey
                        ]))
                    ]
                ));
            } else {
                $player->sendForm(new MenuForm(
                    "§8• §aVerification §8•§r§f", BattleCore::getInstance()->getLanguageSystem()->translate($player, "verification.status.true"),
                    [
                        new Button("Discord Name: " . $player->getDiscordName())
                    ]
                ));
            }
        }

    }

}