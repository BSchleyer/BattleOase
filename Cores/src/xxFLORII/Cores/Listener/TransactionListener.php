<?php

namespace xxFLORII\Cores\Listener;

use xxFLORII\Cores\Main;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;

class TransactionListener implements Listener {

    public function onTransaction(InventoryTransactionEvent $event){
        $cfg = Main::getInstance()->getConfig();
        if ($cfg->get("ingame") === false){
            $event->cancel();
        }
    }

}