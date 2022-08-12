<?php


namespace battleoase\bedwars\listener;


use battleoase\battlecore\BattleCore;
use battleoase\bedwars\api\TeamAPI;
use battleoase\bedwars\BedWars;
use battleoase\bedwars\classes\Team;
use battleoase\bedwars\player\BedWarsPlayer;
use battleoase\bedwars\player\KnockPlayer;
use battleoase\bedwars\player\PlayerManager;
use battleoase\bedwars\utils\PlayerScoreboard;
use BattleOase\ReplaySystemPlayer\scheduler\PlayReplayTask;
use iTzFreeHD\ClanSystem\api\ClanAPI;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use xenialdan\apibossbar\BossBar;

class PlayerJoinListener implements Listener {

    /**
     * @param PlayerJoinEvent $event
     */
    public function onPlayerJoin(PlayerJoinEvent $event)
    {
    	$event->setJoinMessage("");
        BedWars::getInstance()->getPlayerManager()->registerPlayer(new BedWarsPlayer($event->getPlayer()));
        TeamAPI::setRandomTeam($event->getPlayer());

		$bwPlayer = BedWars::getInstance()->getPlayerManager()->getPlayer($event->getPlayer()->getName());
		$bwPlayer->onLoad();

		$bar = new BossBar();
		$bar->setTitle(BedWars::PREFIX."§7Waiting for more Players...");
		$bar->setSubTitle("                §3Battle§bOase §r§f§7");
		$bar->setPercentage(1.0);
		$bar->addPlayer($event->getPlayer());
		$bar->showTo([$event->getPlayer()]);

		if (BedWars::getInstance()->ingame == true) {
			$event->getPlayer()->setGamemode(GameMode::SPECTATOR());
			$event->getPlayer()->teleport(Server::getInstance()->getWorldManager()->getWorldByName(BedWars::getInstance()->getArena()->getName())->getSafeSpawn());
		} else {
			BedWars::getInstance()->lastdamager[$event->getPlayer()->getName()] = false;
			BedWars::$i++;
			Server::getInstance()->broadcastMessage("§7[§a+§7] §7" .$event->getPlayer()->getNameTag() . "§7");
		}

		$scoreboard = new PlayerScoreboard();
		$scoreboard->scoreboard($event->getPlayer());
		BedWars::getInstance()->goldvote[$event->getPlayer()->getName()] = false;
		//BedWars::getInstance()->kill[$event->getPlayer()->getName()] = 0;
		BedWars::getInstance()->bed[$event->getPlayer()->getName()] = 0;

    }

    public function onExhaute(PlayerExhaustEvent $event){
    	$event->cancel();
	}

}