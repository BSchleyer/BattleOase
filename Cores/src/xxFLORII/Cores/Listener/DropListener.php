<?php

namespace xxFLORII\Cores\Listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDropItemEvent;

class DropListener implements Listener {

    public function onDrop(PlayerDropItemEvent $event){
        $event->cancel();
    }

}