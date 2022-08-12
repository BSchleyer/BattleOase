<?php


namespace battleoase\bedwars\task;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\joinMeSystem\utils\Utils;
use battleoase\bedwars\api\TeamAPI;
use battleoase\bedwars\BedWars;
use battleoase\bedwars\caches\MapCache;
use battleoase\bedwars\utils\PlayerScoreboard;
use battleunity\lobbycore\game\Game;
use ceepkev77\cloudbridge\CloudBridge;
use ceepkev77\cloudbridge\network\DataPacket;
use ceepkev77\cloudbridge\network\packet\KeepALivePacket;
use ceepkev77\cloudbridge\network\packet\UpdateGameServerInfoPacket;
use pocketmine\item\Bed;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\player\GameMode;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\world\Position;
use pocketmine\world\sound\AnvilUseSound;
use pocketmine\world\sound\BowShootSound;
use pocketmine\world\sound\ClickSound;
use pocketmine\world\sound\XpCollectSound;
use xenialdan\apibossbar\BossBar;

class GameTask extends Task
{
    public function onRun(): void
    {
        $config = new Config(BedWars::getInstance()->getDataFolder() . "config.yml", Config::YAML);
        $minplayer = $config->get("minPlayerInRound");
        if (BedWars::getInstance()->ingame == false) {
			$topMap = array_search(max(MapCache::$votes),MapCache::$votes);
			BedWars::getInstance()->getArena()->setName($topMap);
            if (TeamAPI::getAllUsedTeams() > 1 && count(BedWars::getInstance()->getServer()->getOnlinePlayers()) >= $minplayer) {
                if (!BedWars::getInstance()->countdown == 0) {
					foreach (Server::getInstance()->getOnlinePlayers() as $players) {
						if (BedWars::getInstance()->countdown < 11){
							$players->getInventory()->clearAll();
							$players->removeCurrentWindow();
							if (BedWars::getInstance()->statsMessage == false){
								$players->sendMessage("      ");
								$players->sendMessage(BedWars::PREFIX . "§4Settings for the Round");
								$players->sendMessage("§7» §6Ranking: " . (BedWars::getInstance()->ranked == true ? "§aOn" : "§cOff"));
								$players->sendMessage("§7» §6Map: " . "§r§f".BedWars::getInstance()->getArena()->getName());
								$players->sendMessage("§7» §6Gold: " .((BedWars ::getInstance()->no <= BedWars::getInstance()->yes) ? "§aOn" : "§cOff"));
								$players->sendMessage("   ");
								BedWars::getInstance()->statsMessage = true;
							}
							$players->getWorld()->addSound($players->getPosition()->asVector3(), new ClickSound());
						}
                        if (BedWars::getInstance()->countdown < 5) {
							$players->getWorld()->addSound($players->getPosition()->asVector3(), new XpCollectSound());
							$players->removeCurrentWindow();
                        }
                        if (BedWars::getInstance()->countdown == 1) {
							$players->getWorld()->addSound($players->getPosition()->asVector3(), new AnvilUseSound());
                                $bwplayer = BedWars::getInstance()->getPlayerManager()->getPlayer($players);
								$team = $bwplayer->getTeam();

                                if (is_null($team)){
									TeamAPI::setRandomTeam($players);
								}else{
									if ($team->getName() == "???") {
										$bwplayer->removeTeam();
										TeamAPI::setRandomTeam($players);
									}
								}
                            }
						$players->sendPopup(BedWars::PREFIX . "§e" . BedWars::getInstance()->countdown . " §7Sekunden!");
                    }
                    BedWars::getInstance()->countdown--;
                } else {
                    BedWars::getInstance()->ingame = true;
                    foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                        $map = BedWars::getInstance()->getArena()->getName();
                        $bwplayer = BedWars::getInstance()->getPlayerManager()->getPlayer($player);
                        $team = $bwplayer->getTeam()->getName();
                        if($team === "???") {
                        	$bwplayer->removeTeam();
                        	TeamAPI::setRandomTeam($player);
                            //  BedWars::getInstance()->getTeam()->removePlayerFromTeam($player);
                            //BedWars::getInstance()->getTeam()->getRandomTeam($player);
                        }
                        $bwplayer->getTeam()->setBed(true);
                        $config = new Config("/home/cloud/templates/" . CloudBridge::getInstance()->getTemplate() . "/plugin_data/BedWars/arena/{$map}.yml", Config::YAML);
                        $x = $config->getNested("Spawn.{$team}.x");
                        $y = $config->getNested("Spawn.{$team}.y");
                        $z = $config->getNested("Spawn.{$team}.z");
                        Server::getInstance()->getWorldManager()->loadWorld($map);
                        $player->teleport(new Position($x, $y, $z, Server::getInstance()->getWorldManager()->getWorldByName($map)));

                        $player->getInventory()->clearAll();
                        $player->getXpManager()->setXpLevel(0);
                        $player->getHungerManager()->setFood(20);
                        $player->setHealth(20);

                        BedWars::getInstance()->lastdamager[$player->getName()] = false;

						$packet = new UpdateGameServerInfoPacket();
						$packet->type = $packet->TYPE_UPDATE_STATE_MODE;
						$packet->value = 1; // 1= INAGME
						$packet->sendPacket();
						BedWars::getInstance()->getScheduler()->scheduleDelayedTask(new StartTask(), 10);

						$player->sendMessage(BedWars::PREFIX . "§aDiese Runde wird aufgenommen!");
						BattleCore::getInstance()->replaySystemRecorder->getReplay()->startReplay(Server::getInstance()->getWorldManager()->getWorldByName($map), new Position($x, $y, $z, Server::getInstance()->getWorldManager()->getWorldByName($map)));
                    }
                }
            }

        } else {
			if (TeamAPI::getAllUsedTeams() <= 1) {
				BedWars::getInstance()->saveDamager = true;
				BedWars::getInstance()->ingame = false;
				BattleCore::getInstance()->replaySystemRecorder->getReplay()->stopReplay();

				if(count(BedWars::getInstance()->getServer()->getOnlinePlayers()) == 0) {
					Server::getInstance()->shutdown();
				}
				BedWars::getInstance()->statsMessage = false;
				foreach(BedWars::getInstance()->getServer()->getOnlinePlayers() as $player) {
					$player->sendMessage(BedWars::PREFIX . "§7Your Replay-ID: §e§l" . "§cNOT EXIST");
					(new BossBar())->removePlayer($player);
					//Todo: Check with TeamAPI
					if($player->getGamemode() === GameMode::SURVIVAL() or $player->getGamemode() === GameMode::CREATIVE() ) {
						BattleCore::getInstance()->statsSystem->addWin($player, "BedWars");
					} else {
						BattleCore::getInstance()->statsSystem->addLose($player, "BedWars");
					}
					$sb = new PlayerScoreboard();
					$sb->removeScoreboard($player, "ingame");
					$player->teleport(Server::getInstance()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
					$player->setGamemode(GameMode::ADVENTURE());
					$player->setHealth(20);
					$player->getInventory()->clearAll();
					$player->getArmorInventory()->clearAll();
					$player->getCursorInventory()->setItem(0, ItemFactory::getInstance()->get(0, 0, 0));
                    $player->getInventory()->setContents(BedWars::getInstance()->loadContents(BedWars::END_ITEMS));
					$packet = new UpdateGameServerInfoPacket();
					$packet->type = $packet->TYPE_UPDATE_STATE_MODE;
					$packet->value = 1; // 1= INAGME
					$packet->sendPacket();
					BedWars::getInstance()->statsMessage = true;
				}

				BedWars::getInstance()->getScheduler()->scheduleRepeatingTask(new class() extends Task{
					public int $count = 30;

					public function onRun(): void
					{

						if(!$this->count == 0) {
							foreach (Server::getInstance()->getOnlinePlayers() as $player) {
								$player->sendPopup("§7Server restarting in " . $this->count . " Seconds");
							}
							$this->count--;
						} else {
							Server::getInstance()->shutdown();
						}
					}
				}, 20);
				$this->onCancel();
				$this->getHandler()->cancel();

			}

			//Todo: Add Play-Time
			//Todo: Start recording (ReplaySystem)
        }
    }

}