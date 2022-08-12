<?php

namespace battleoase\battlecore\emojiSystem\listener;

use battleoase\battlecore\BattleCore;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;

class PlayerEmojiChatListener implements Listener {

	public function onChat(PlayerChatEvent $event) {
		$emojiList = BattleCore::getInstance()->emojiSystem::EMOJIS;
		foreach ($emojiList as $item => $value) {
			
		}
	}
}