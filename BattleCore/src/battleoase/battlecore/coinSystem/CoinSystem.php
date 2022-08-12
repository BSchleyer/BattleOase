<?php

namespace battleoase\battlecore\coinSystem;

use battleoase\battlecore\BattleCore;
use battleoase\battlecore\player\BattlePlayer;
use battleoase\battlecore\player\provider\PlayersProvider;
use battleoase\battlecore\util\BPlugin;
use battleoase\battlecore\util\InstantiableTrait;

class CoinSystem extends BPlugin {
    use InstantiableTrait;

    public function __construct() {
        self::$instance = $this;
    }

    /**
     * @param $xboxId
     * @param $coins
     * @return void
     */
    public function setCoins($xboxId, $coins) {
        PlayersProvider::update(BattleCore::getInstance()->getConnection(), $xboxId, [
            "coins" => $coins,
        ]);
    }

    /**
     * @param $xboxId
     * @param $coins
     * @return void
     */
    public function addCoins($xboxId, $coins) {
        $this->setCoins($xboxId, $this->getCoins($xboxId) + $coins);
    }

    /**
     * @param $xboxId
     * @param $coins
     * @return void
     */
    public function removeCoins($xboxId, $coins) {
        $this->setCoins($xboxId, $this->getCoins($xboxId) - $coins);
    }

    /**
     * @param $xboxId
     * @return int|null
     */
    public function getCoins($xboxId): ?int {
        return intval(PlayersProvider::get(BattleCore::getInstance()->getConnection(), $xboxId)["coins"]);
    }
}