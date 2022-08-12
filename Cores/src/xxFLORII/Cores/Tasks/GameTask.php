<?php

namespace xxFLORII\Cores\Tasks;

use battleoase\battlecore\BattlePlayer;
use ceepkev77\cloudbridge\network\packet\UpdateGameServerInfoPacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use xxFLORII\Cores\API\CoresAPI;
use xxFLORII\Cores\Main;

class GameTask extends Task
{

	public function numberPacket(Player $player, $score = 1, $msg = ""): void {
		$entrie = new ScorePacketEntry();

		$entrie->objectiveName = "boots";
		$entrie->type = 3;
		$entrie->customName = str_repeat("", 5) . $msg . str_repeat(" ", 1);
		$entrie->score = $score;
		$entrie->scoreboardId = $score;

		$pk = new SetScorePacket();

		$pk->type = 1;
		$pk->entries[] = $entrie;
		$player->getNetworkSession()->sendDataPacket($pk);
		$pk2 = new SetScorePacket();


		$pk2->entries[] = $entrie;
		$pk2->type = 0;
		$player->getNetworkSession()->sendDataPacket($pk2);
	}

	/**
	 * Function getIcon
	 * @param bool $value
	 * @return string
	 */
	public function getIcon(bool $value): string{
		return ($value ? "§a✔" : "§c✘");
	}

    public function onRun(): void
    {
		$players = Server::getInstance()->getOnlinePlayers();
		$config = Main::getInstance()->getConfig();
		$time = $config->get("time", 20);

		foreach ($players as $player){
			$pk = new SetDisplayObjectivePacket();
			$pk->displaySlot = "sidebar";
			$pk->objectiveName = "boots";
			$pk->displayName = " §3•§b● §b§lBattleOase.NET §b●§3•";
			$pk->criteriaName = "dummy";
			$pk->sortOrder = 0;
			$player->getNetworkSession()->sendDataPacket($pk);


			if ($config->get("ingame") === false) {
				$this->numberPacket($player, 1, "§7");
				$this->numberPacket($player, 2, "  §8» §6Name§f: §7" . $player->getName());
				$this->numberPacket($player, 3, "  §8» §6Waiting§f: §7" . $time);
			} elseif ($config->get("ingame") === true){

				Main::getInstance()->getScheduler()->scheduleDelayedTask(new class() extends Task{
					public function onRun(): void
					{
						$packet = new UpdateGameServerInfoPacket();
						$packet->type = $packet->TYPE_UPDATE_STATE_MODE;
						$packet->value = 1;
						$packet->sendPacket();
					}
				}, 30);

				$red_right = $config->get("block1") === true;
				$red_left = $config->get("block2") === true;
				$blue_right = $config->get("block3") === true;
				$blue_left = $config->get("block4") === true;

				$player_team = in_array($player->getName(), Main::$redTeam) ? "§4Red" ?? in_array($player->getName(), Main::$blueTeam) : "§1Blue";

				$red_count = count(Main::$redTeam);
				$blue_count = count(Main::$blueTeam);

				$this->numberPacket($player, 1, "§7");
				$this->numberPacket($player, 2, "§8» §4Red §7( §e{$red_count} §7)§8:");
				$this->numberPacket($player, 3, "   §8» §cRight Core§f: " . $this->getIcon($red_right));
				$this->numberPacket($player, 4, "   §8» §cLeft Core§f: " . $this->getIcon($red_left));
				$this->numberPacket($player, 5, "§7");
				$this->numberPacket($player, 6, "§8» §1Blue §7( §e{$blue_count} §7)§8:");
				$this->numberPacket($player, 7, "   §8» §1Right Core§f: " . $this->getIcon($blue_right));
				$this->numberPacket($player, 8, "   §8» §1Left Core§f: " . $this->getIcon($blue_left));
			}
		}

		$countPlayers = count(Server::getInstance()->getOnlinePlayers());

		if ($config->get("ingame") === false && $config->get("reset") === false) {
			if ($countPlayers < 2 && count(Main::$redTeam) < 1 || count(Main::$blueTeam) < 1) {
				foreach ($players as $player) {
					$player->sendTip("§7Waiting for more players...");
				}
			}elseif (count(Main::$redTeam) >= 1 || count(Main::$blueTeam) >= 1) {
				$config->set("time", $config->get("time")-1);
				$config->save();
				foreach ($players as $onlinePlayer) {
					$onlinePlayer->sendTip("§cStart in: §e{$time}");
				}
				if ($time <= 0) {
					$config->set("ingame", true);
					$config->set("state", true);
					$config->set("block1", true);
					$config->set("block2", true);
					$config->set("block3", true);
					$config->set("block4", true);
					$config->save();

					Server::getInstance()->getWorldManager()->loadWorld($config->get("Arena"), true);

					foreach ($players as $onlinePlayer) {
						(new CoresAPI())->teleportIngame($onlinePlayer);
						(new CoresAPI())->giveKit($onlinePlayer);
						$onlinePlayer->setHealth(20);
						$onlinePlayer->getHungerManager()->setFood(20);
						(new CoresAPI())->spawn($onlinePlayer);
					}
				}
			}
		} else if ($config->get("ingame") === true) {

			$all = Main::getInstance()->getServer()->getOnlinePlayers();

			foreach ($all as $p){
				$p->setScoreTag("§c" . $p->getHealth() . "❤");
			}
			if (count($all) <= 1) {

				$config->set("ingame", false);
				$config->set("reset", true);
				$config->set("rtime", 10);
				$config->set("time", 60);
				$config->set("playtime", 3600);
				$config->set("block1", false);
				$config->set("block2", false);
				$config->set("block3", false);
				$config->set("block4", false);
				$config->save();
				foreach ($all as $player) {

					$player->getInventory()->clearAll();
					$player->setHealth(20);
					$player->getHungerManager()->setFood(20);
					$player->getEffects()->clear();

					$player->sendMessage(Main::getPrefix() . "§aYour team has won this game.");

					$spawn = Server::getInstance()->getWorldManager()->getDefaultWorld()->getSafeSpawn();
					Server::getInstance()->getWorldManager()->getDefaultWorld()->loadChunk($spawn->getX(), $spawn->getZ());
					$player->teleport($spawn, 0, 0);
					$levelname = $config->get("Arena");
					$lev = Server::getInstance()->getWorldManager()->getWorldByName($levelname);
					Server::getInstance()->getWorldManager()->unloadWorld($lev);
					Main::getCoresAPI()->deleteDirectory(Main::getInstance()->getServer()->getDataPath() . "/worlds/" . $levelname);
					Main::getCoresAPI()->copymap(Main::getInstance()->getDataFolder() . "/maps/" . $levelname, Main::getInstance()->getServer()->getDataPath() . "/worlds/" . $levelname);
					Server::getInstance()->getWorldManager()->loadWorld($levelname, true);

				}

			} elseif (count($all) >= 2) {

				$config->set("playtime", $config->get("playtime") - 1);
				$config->save();
				$time = $config->get("playtime") + 1;

				if ($config->get("block1") === false && $config->get("block2") === false or count(Main::$redTeam) <= 0) {

					$config->set("ingame", false);
					$config->set("reset", true);
					$config->set("rtime", 10);
					$config->set("time", 60);
					$config->set("playtime", 3600);
					$config->set("block1", false);
					$config->set("block2", false);
					$config->set("block3", false);
					$config->set("block4", false);
					$config->save();

					Main::$winnerTeam = "§1Blue";
					if (count(Main::$blueTeam) >= 1) {
						foreach (Main::$blueTeam as $team) {
							$wplayer = Server::getInstance()->getPlayerExact($team);
							if ($wplayer !== null) {
								$wplayer->sendMessage(Main::getPrefix() . "§aYour team has won this game.");
							}
						}
					}
				} elseif ($config->get("block3") === false && $config->get("block4") === false or count(Main::$blueTeam) <= 0) {

					$config->set("ingame", false);
					$config->set("reset", true);
					$config->set("rtime", 10);
					$config->set("time", 60);
					$config->set("playtime", 3600);
					$config->set("block1", false);
					$config->set("block2", false);
					$config->set("block3", false);
					$config->set("block4", false);
					$config->save();

					Main::$winnerTeam = "§4Red";
					if (count(Main::$redTeam) >= 1) {
						foreach (Main::$redTeam as $team) {
							$wplayer = Server::getInstance()->getPlayerExact($team);
							if ($wplayer !== null) {
								$wplayer->sendMessage(Main::getPrefix() . "§aYour team has won this game.");
							}
						}
					}
				}

				foreach ($players as $player) {

					if ($config->get("block1") === false && $config->get("block2") === false or count(Main::$redTeam) <= 0) {
						$player->getInventory()->clearAll();
						$player->setHealth(20);
						$player->getHungerManager()->setFood(20);
						$player->getEffects()->clear();

						$spawn = Server::getInstance()->getWorldManager()->getDefaultWorld()->getSafeSpawn();
						Server::getInstance()->getWorldManager()->getDefaultWorld()->loadChunk($spawn->getX(), $spawn->getZ());
						$player->teleport($spawn, 0, 0);
						$levelname = $config->get("Arena");
						$lev = Server::getInstance()->getWorldManager()->getWorldByName($levelname);
						Server::getInstance()->getWorldManager()->unloadWorld($lev);
						Main::getCoresAPI()->deleteDirectory(Main::getInstance()->getServer()->getDataPath() . "/worlds/" . $levelname);
						Main::getCoresAPI()->copymap(Main::getInstance()->getDataFolder() . "/maps/" . $levelname, Main::getInstance()->getServer()->getDataPath() . "/worlds/" . $levelname);
						Server::getInstance()->getWorldManager()->loadWorld($levelname, true);

					}

					if ($config->get("block3") === false && $config->get("block4") === false or count(Main::$blueTeam) <= 0) {

						$player->getInventory()->clearAll();
						$player->setHealth(20);
						$player->getHungerManager()->setFood(20);
						$player->getEffects()->clear();

						$spawn = Server::getInstance()->getWorldManager()->getDefaultWorld()->getSafeSpawn();
						Server::getInstance()->getWorldManager()->getDefaultWorld()->loadChunk($spawn->getX(), $spawn->getZ());
						$player->teleport($spawn, 0, 0);
						$levelname = $config->get("Arena");
						$lev = Server::getInstance()->getWorldManager()->getWorldByName($levelname);
						Server::getInstance()->getWorldManager()->unloadWorld($lev);
						Main::getCoresAPI()->deleteDirectory(Main::getInstance()->getServer()->getDataPath() . "/worlds/" . $levelname);
						Main::getCoresAPI()->copymap(Main::getInstance()->getDataFolder() . "/maps/" . $levelname, Main::getInstance()->getServer()->getDataPath() . "/worlds/" . $levelname);
						Server::getInstance()->getWorldManager()->loadWorld($levelname, true);

					}

					if ($time % 60 === 0 && $time > 60 && $time < 3600) {

						$player->sendMessage(Main::getPrefix() . "§cThe game ends in §e" . $time / 60 . "§c minutes.");

					} else if ($time === 60) {

						$player->sendMessage(Main::getPrefix() . "§cThe game ends in §e" . $time . "§c seconds.");

					} else if ($time === 1 || $time === 2 || $time === 3 || $time === 4 || $time === 5 || $time === 15 || $time === 30) {

						$player->sendMessage(Main::getPrefix() . "§cThe game ends in §e" . $time . "§c seconds.");

					} else if ($time === 0) {

						$player->getInventory()->clearAll();
						$player->setHealth(20);
						$player->getHungerManager()->setFood(20);
						$player->getEffects()->clear();
						$player->sendMessage(Main::getPrefix() . "§cNobody won.");
						$spawn = Server::getInstance()->getWorldManager()->getDefaultWorld()->getSafeSpawn();
						Server::getInstance()->getWorldManager()->getDefaultWorld()->loadChunk($spawn->getX(), $spawn->getZ());
						$player->teleport($spawn, 0, 0);
						$config->set("ingame", false);
						$config->set("reset", true);
						$config->set("rtime", 10);
						$config->set("time", 60);
						$config->set("playtime", 3600);
						$config->set("block1", false);
						$config->set("block2", false);
						$config->set("block3", false);
						$config->set("block4", false);
						$config->set("player1", "");
						$config->set("player2", "");
						$config->set("player3", "");
						$config->set("player4", "");
						$config->save();
						$levelname = $config->get("Arena");
						$lev = Server::getInstance()->getWorldManager()->getWorldByName($levelname);
						Server::getInstance()->getWorldManager()->unloadWorld($lev);
						Main::getCoresAPI()->deleteDirectory(Main::getInstance()->getServer()->getDataPath() . "/worlds/" . $levelname);
						Main::getCoresAPI()->copymap(Main::getInstance()->getDataFolder() . "/maps/" . $levelname, Main::getInstance()->getServer()->getDataPath() . "/worlds/" . $levelname);
						Server::getInstance()->getWorldManager()->loadWorld($levelname, true);
					}
				}
			}
		}
        if ($config->get("reset") === true) {
			$players = Server::getInstance()->getOnlinePlayers();
            $config->set("rtime", $config->get("rtime") - 1);
            $config->save();
            $time = $config->get("rtime") + 1;
            foreach ($players as $player) {
            	if ($player instanceof BattlePlayer){
					if ($time === 10) {
						$player->sendMessage(Main::getPrefix() . "§cThe Server is restarting in §e{$time} §cseconds§8.");
					} else if ($time === 5) {
						$player->sendMessage(Main::getPrefix() . "§cThe Server is restarting in §e{$time} §cseconds§8.");
					} else if ($time === 4) {
						$player->sendMessage(Main ::getPrefix() . "§cThe Server is restarting in §e{$time} §cseconds§8.");
					}else if ($this === 3){
						$player->sendMessage(Main ::getPrefix() . "§cThe Server is restarting in §e{$time} §cseconds§8.");
					} else if ($time === 2){
						$player->sendMessage(Main::getPrefix() . "§cPrepare Transfering...");
						$player->kick("FALLBACK");
					} else if ($time === 1) {
						$player->sendMessage(Main::getPrefix() . "§cThe Server is restarting now§8.");
						$config->set("reset", false);
						$config->set("rtime", 10);
						$config->set("state", false);
						$config->set("block1", true);
						$config->set("block2", true);
						$config->set("block3", true);
						$config->set("block4", true);
						$config->save();
						Server::getInstance()->shutdown();
					}
				}
            }
        }
    }
}