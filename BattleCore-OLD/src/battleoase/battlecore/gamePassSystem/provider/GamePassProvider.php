<?php

namespace battleoase\battlecore\gamePassSystem\provider;

use battleoase\battlecore\BattleCore;
use battleoase\battlecore\BattlePlayer;
use battleoase\battlecore\languageSystem\objects\Translation;
use pocketmine\player\Player;
use pocketmine\Server;

class GamePassProvider {

    public function unlock(Player $player, int $season): void
    {
        BattleCore::getInstance()->getConnection()->execute("INSERT INTO `Core`.`unlock_gamepass_players`(`playerName`, `season`) VALUES ('{$player->getName()}','$season')",
            "Core",
            function ($result, $extra) {
                $playerName = $extra["playerName"];
                $player = Server::getInstance()->getPlayerByPrefix($playerName);
                if ($player instanceof BattlePlayer) {
                    if ($player->isConnected()) {
                        $player->sendMessage(Translation::make("gamePass.message.unlock"));
                    }
                }
            },
            ["playerName" => $player->getName()]);
    }

}