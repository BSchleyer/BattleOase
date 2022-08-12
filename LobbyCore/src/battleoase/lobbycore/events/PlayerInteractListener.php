<?php


namespace battleoase\lobbycore\events;

use battleoase\battlecore\customInteractSystem\events\PlayerInteractEventWithDelay;
use battleoase\lobbycore\forms\FeatureForm;
use battleoase\lobbycore\forms\LobbySwitcherForm;
use battleoase\lobbycore\forms\ProfileForm;
use battleoase\lobbycore\forms\TeleporterForm;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;

class PlayerInteractListener implements Listener
{
	public function onInteract(PlayerInteractEventWithDelay $event){
		$item = $event->getPlayer()->getInventory()->getItemInHand();
		switch ($item->getCustomName()){
			case "§8• §eTeleporter §8•":
				$tform = new TeleporterForm($event->getPlayer());
				$tform->open();
				break;
			case "§8• §6Feature §8•":
				$fform = new FeatureForm($event->getPlayer());
				$fform->open();
				break;
			case "§8• §bLobby-Switcher §8•":
				$lform = new LobbySwitcherForm($event->getPlayer());
				$lform->open();
				break;
			case "§8• §3Profile §8•":
				$pform = new ProfileForm($event->getPlayer());
				$pform->open();
				break;
		}
	}

    /**
     * @param DataPacketSendEvent $ev
     * @return void
     */
    public function onDataPacketSend(DataPacketSendEvent $ev): void
    {
        foreach($ev->getPackets() as $packet)
        {
            /** @var LevelSoundEventPacket $packet */
            if ($packet->pid() == LevelSoundEventPacket::NETWORK_ID)
            {
                if($packet->sound === LevelSoundEvent::ATTACH) $ev->cancel();
                elseif($packet->sound === LevelSoundEvent::ATTACK_NODAMAGE) $ev->cancel();
                elseif($packet->sound === LevelSoundEvent::ATTACK_STRONG) $ev->cancel();
            }
        }
    }
}