<?php


namespace battleoase\lobbycore\jumpAndRun\events;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\BattlePlayer;
use battleoase\lobbycore\jumpAndRun\commands\JumpLeaveCommand;
use battleoase\lobbycore\jumpAndRun\JumpAndRun;
use battleoase\lobbycore\LobbyCore;
use battleoase\lobbycore\player\PlayerManager;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\EmeraldOre;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\inventory\CraftItemEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\math\Vector3;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\Server;

class PlayerMoveListener implements Listener {

	public function onMove(PlayerMoveEvent $event) {
		$player = $event->getPlayer();
		$block = $player->getWorld()->getBlock(new Vector3($player->getPosition()->getX(), $player->getPosition()->getY() - 0.5, $player->getPosition()->getZ()));


		if ($player instanceof BattlePlayer){
			if($block->getId() == VanillaBlocks::EMERALD()->getId()){
				if(LobbyCore::getInstance()->getJumpAndRun()->jump[$player->getName()] == false) {
					LobbyCore::getInstance()->getJumpAndRun()->checkpoint[$player->getName()] = $block->getPosition()->x . ":" . $block->getPosition()->y . ":" . $block->getPosition()->z;
					LobbyCore::getInstance()->getJumpAndRun()->jump[$player->getName()] = true;
					$player->getInventory()->clearAll();
					$player->setAllowFlight(false);
					$player->setFlying(false);
					$player->sendMessage(LobbyCore::getInstance()->getJumpAndRun()->getPrefix() . BattleCore::getInstance()->getLanguageSystem()->translate($player, "jumpAndRun.message.start"));
					$player->sendMessage(LobbyCore::getInstance()->getJumpAndRun()->getPrefix() . BattleCore::getInstance()->getLanguageSystem()->translate($player, "jumpAndRun.notice.jumpLeave"));
					foreach (Server::getInstance()->getOnlinePlayers() as $p) {
						$player->hidePlayer($p);
					}
				}

			} else if ($block->getId() == VanillaBlocks::GOLD()->getId()){
				if(LobbyCore::getInstance()->getJumpAndRun()->jump[$player->getName()] == true) {
					if($block->getPosition()->x . ":" . $block->getPosition()->y . ":" . $block->getPosition()->z == LobbyCore::getInstance()->getJumpAndRun()->checkpoint[$player->getName()]) {
						LobbyCore::getInstance()->getJumpAndRun()->checkpoint[$player->getName()] = $block->getPosition()->x . ":" . $block->getPosition()->y . ":" . $block->getPosition()->z;
					} else {
						LobbyCore::getInstance()->getJumpAndRun()->checkpoint[$player->getName()] = $block->getPosition()->x . ":" . $block->getPosition()->y . ":" . $block->getPosition()->z;
						$rndm = mt_rand(1, 5);

						$player->addcoins($rndm);
						$player->sendMessage(LobbyCore::getInstance()->getJumpAndRun()->getPrefix() . BattleCore::getInstance()->getLanguageSystem()->translate($player, "jumpAndRun.message.checkpoint"));
					}

				}
			} elseif($block->getId() == VanillaBlocks::DIAMOND()->getId()) {

				if(LobbyCore::getInstance()->getJumpAndRun()->jump[$player->getName()] == true) {
					LobbyCore::getInstance()->getJumpAndRun()->jump[$player->getName()] = false;
					PlayerManager::getPlayer($event->getPlayer())->giveItems();
					$rndm = mt_rand(5, 60);

					$player->addcoins($rndm);
					$player->sendMessage(LobbyCore::getInstance()->getJumpAndRun()->getPrefix() . BattleCore::getInstance()->getLanguageSystem()->translate($player, "jumpAndRun.message.finish"));
					unset(LobbyCore::getInstance()->getJumpAndRun()->checkpoint[$player->getName()]);
					foreach (Server::getInstance()->getOnlinePlayers() as $p) {
						$player->showPlayer($p);
					}
					$player->teleport(new Vector3(-42752, 49, -5888));
				}
			} elseif(LobbyCore::getInstance()->getJumpAndRun()->jump[$player->getName()] == true) {
				if($player->getGamemode() === GameMode::CREATIVE() || $player->getGamemode() === GameMode::SPECTATOR() or $player->getAllowFlight()) {
					$player->setAllowFlight(false);
					$player->setFlying(false);
					$player->setGamemode(GameMode::SURVIVAL());
				}
			}

			if ($event->getPlayer()->getPosition()->getY() < 42) {
				//PlayerManager::getPlayer($event->getPlayer())->giveItems();
				if(LobbyCore::getInstance()->getJumpAndRun()->jump[$player->getName()] == true) {
					list($x, $y, $z) = explode(":", LobbyCore::getInstance()->getJumpAndRun()->checkpoint[$player->getName()]);
					$player->teleport(new Vector3((int)$x, (int)$y + 1, (int)$z));

					//	$this->jump[$player->getName()] = false;
					//	$player->sendMessage(LobbyCore::getInstance()->getJumpAndRun()->getPrefix() . BattleCore::getInstance()->getLanguageSystem()->translate($player, "jumpAndRun.message.lose"));
					//	foreach (Server::getInstance()->getOnlinePlayers() as $p) {
					//		$player->showPlayer($p);
					//	}
				}
			}
		}

	}

	public function craft(CraftItemEvent $eventPacket) {
		$eventPacket->cancel();
	}


	public function PlayerJoin(PlayerJoinEvent $event) {
		LobbyCore::getInstance()->getJumpAndRun()->jump[$event->getPlayer()->getName()] = false;
	}
}