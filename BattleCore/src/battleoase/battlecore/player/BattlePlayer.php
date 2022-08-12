<?php

namespace battleoase\battlecore\player;

use battleoase\battlecore\coinSystem\CoinSystem;
use battleoase\battlecore\player\object\InitializationData;
use battleoase\battlecore\player\provider\PlayersProvider;
use battleoase\battlecore\util\AsyncExecutor;
use pocketmine\entity\Location;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\player\Player;
use pocketmine\player\PlayerInfo;
use pocketmine\Server;

class BattlePlayer extends Player {

    public function __construct(Server $server, NetworkSession $session, PlayerInfo $playerInfo, bool $authenticated, Location $spawnLocation, CompoundTag $namedtag) {
        parent::__construct($server, $session, $playerInfo, $authenticated, $spawnLocation, $namedtag);
    }

    public function initialize(): void {
        $xboxId = $this->getXuid();
        $name = $this->getName();
        AsyncExecutor::submitAsyncTask(function (\mysqli $mysqli) use ($xboxId, $name) {
            PlayersProvider::register($mysqli, $xboxId, $name);
            $playersData = PlayersProvider::get($mysqli, $xboxId);
            if($playersData["name"] !== $name) {
                PlayersProvider::update($mysqli, $xboxId, ["name" => $name]);
            }
            return new InitializationData(
                $name,
                $xboxId,
                intval($playersData["coins"]),
                intval($playersData["onlinetime"]),
                $playersData["extra"]
            );
        }, function (InitializationData $data) {
            if(!$this->isConnected()) return;
            $this->sendMessage("Player Data Loading successful");
        });
    }

}