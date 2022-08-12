<?php

namespace xxFLORII\Cores\Listener;

use pocketmine\block\BlockLegacyIds;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\math\Vector3;
use pocketmine\player\GameMode;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as Color;
use pocketmine\world\sound\GhastShootSound;
use xxFLORII\Cores\Main;

class BreakListener implements Listener {

    public function onBreak(BlockBreakEvent $event){
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $pos = $block->getPosition();
        $x = $pos->getX();
        $y = $pos->getY();
        $z = $pos->getZ();
        $config = Main::getInstance()->getConfig();
        $af = new Config(Main::getInstance()->getDataFolder() . "/" . $config->get("Arena") . ".yml", Config::YAML);
        if ($config->get("ingame") === false) {
            $event->cancel();
        } else {
            if ($block->getId() === BlockLegacyIds::BEACON) {
                $event->setDrops([]);
                if ($x === $af->get("sb1x") && $y === $af->get("sb1y") && $z === $af->get("sb1z")) {
                    if (in_array($player->getName(), Main::$blueTeam)) {
                        $event->cancel();
                        $player->sendMessage(Main::getPrefix() . Color::RED . "You can't destroy your own Core!");
                    } else {
                        $config->set("block3", false);
                        $config->save();

                        foreach (Main::$blueTeam as $teamPlayer) {
                            $tp = Server::getInstance()->getPlayerExact($teamPlayer);
                            if ($tp !== null) {
                                $tp->sendMessage(Main::getPrefix() . "§cYour §1Right Core §cwas destroyed!");
                                $tp->sendTitle("§1Right Core", "§4destroyed!");
								$tp->sendTitle(" ", "§r" . str_repeat("\n", 6) . str_repeat(" ", 36) . "§1Right Core\n§4destroyed!", 1, 18, 1);
                            }
                        }

                        $player->sendMessage(Main::getPrefix() . "§cThe §1Right Core §cwas destroyed§8.");
                        foreach (Server::getInstance()->getOnlinePlayers() as $oPlayer) {
                            $player->getWorld()->addSound(new Vector3($player->getPosition()->getX(), $player->getPosition()->getY(), $player->getPosition()->getZ()), new GhastShootSound());
                            $oPlayer->sendMessage(Main::getPrefix() . "§cThe §1Right Core §cwas destroyed by §r{$player->getDisplayName()}§8.");
                        }

                    }
                } else if ($x === $af->get("sb2x") && $y === $af->get("sb2y") && $z === $af->get("sb2z")) {
                    if (in_array($player->getName(), Main::$blueTeam)) {
                        $event->cancel();
                        $player->sendMessage(Main::getPrefix() . Color::RED . "You can't destroy your own Core!");
                    } else {
                        $config->set("block4", false);
                        $config->save();

                        foreach (Main::$blueTeam as $teamPlayer) {
                            $tp = Server::getInstance()->getPlayerExact($teamPlayer);
                            if ($tp !== null) {
                                $tp->sendMessage(Main::getPrefix() . "§cYour §1Left Core §cwas destroyed!");
                               // $tp->sendTitle("§1Left Core", "§4destroyed!");
								$tp->sendTitle(" ", "§r" . str_repeat("\n", 6) . str_repeat(" ", 36) . "§1Left Core\n§4destroyed!", 1, 18, 1);
                            }
                        }

                        $player->sendMessage(Main::getPrefix() . "§cThe §1Left Core §cwas destroyed§8.");
                        foreach (Server::getInstance()->getOnlinePlayers() as $oPlayer) {
                            $player->getWorld()->addSound(new Vector3($player->getPosition()->getX(), $player->getPosition()->getY(), $player->getPosition()->getZ()), new GhastShootSound());
                            $oPlayer->sendMessage(Main::getPrefix() . "§cThe §1Left Core §cwas destroyed by §r{$player->getDisplayName()}§8.");
                        }
                    }
                } else if ($x === $af->get("sr1x") && $y === $af->get("sr1y") && $z === $af->get("sr1z")) {
                    if (in_array($player->getName(), Main::$redTeam)) {
                        $event->cancel();
                        $player->sendMessage(Main::getPrefix() . Color::RED . "You can't destroy your own Core!");
                    } else {
                        $config->set("block1", false);
                        $config->save();

                        foreach (Main::$redTeam as $teamPlayer) {
                            $tp = Server::getInstance()->getPlayerExact($teamPlayer);
                            if ($tp !== null) {
                                $tp->sendMessage(Main::getPrefix() . "§cYour §4Right Core §cwas destroyed!");
								$tp->sendTitle(" ", "§r" . str_repeat("\n", 6) . str_repeat(" ", 36) . "§cRight Core\n§4destroyed!", 1, 18, 1);
                                //$tp->sendTitle("§cRight Core", "§4destroyed!");
                            }
                        }

                        $player->sendMessage(Main::getPrefix() . "§cThe §4Right Core §cwas destroyed§8.");
                        //LanguageAPI::sendMessage($player, "message.coreDestroyed", ["§4Right Core", "§4Red"]);
                        foreach (Server::getInstance()->getOnlinePlayers() as $oPlayer) {
                            $player->getWorld()->addSound(new Vector3($player->getPosition()->getX(), $player->getPosition()->getY(), $player->getPosition()->getZ()), new GhastShootSound());
                            $oPlayer->sendMessage(Main::getPrefix() . "§cThe §4Right Core §cwas destroyed by §r{$player->getDisplayName()}§8.");
                        }
                    }
                } else if ($x === $af->get("sr2x") && $y === $af->get("sr2y") && $z === $af->get("sr2z")) {
                    if (in_array($player->getName(), Main::$redTeam)) {
                        $event->cancel();
                        $player->sendMessage(Main::getPrefix() . Color::RED . "You can't destroy your own Core!");
                    } else {
                        $config->set("block2", false);
                        $config->save();

                        foreach (Main::$redTeam as $teamPlayer) {
                            $tp = Server::getInstance()->getPlayerExact($teamPlayer);
                            if ($tp !== null) {
                                $tp->sendMessage(Main::getPrefix() . "§cYour §4Left Core §cwas destroyed!");
                                //$tp->sendTitle("§cLeft Core", "§4destroyed!");
								$tp->sendTitle(" ", "§r" . str_repeat("\n", 6) . str_repeat(" ", 36) . "§cLeft Core\n§4destroyed!", 1, 18, 1);
                            }
                        }

                        $player->sendMessage(Main::getPrefix() . "§cThe §4Left Core §cwas destroyed§8.");
                        foreach (Server::getInstance()->getOnlinePlayers() as $oPlayer) {
                            $player->getWorld()->addSound(new Vector3($player->getPosition()->getX(), $player->getPosition()->getY(), $player->getPosition()->getZ()), new GhastShootSound());
                            $oPlayer->sendMessage(Main::getPrefix() . "§cThe §4Left Core §cwas destroyed by §r{$player->getDisplayName()}§8.");
                        }
                    }
                }
            } else {
				$pos = $block->getPosition()->asVector3();
				$arr = [$pos->getX(), $pos->getY(), $pos->getZ()];
				if (!in_array($block->getId(), Main::$breakableBlocks) && !in_array($arr, Main::$placedBlocks)) {
					$player->sendMessage("§cYou can't break this block.");
					$event->cancel();
				} elseif (in_array($arr, Main::$placedBlocks)){
					$k = array_search($arr, Main::$placedBlocks);
					unset(Main::$placedBlocks[$k]);#
					$event->uncancel();
				}
            }
        }
        if (Main::getInstance()->mode === 1 && $player->hasPermission("cores.admin")) {

            $af->set("s1x", $pos->getX() + 0.5);
            $af->set("s1y", $pos->getY() + 1);
            $af->set("s1z", $pos->getZ() + 0.5);
            $af->save();

            $player->sendMessage(Main::getPrefix() . "Now the blue spawn§8.");
            Main::getInstance()->mode++;
            $event->cancel();

        } else if (Main::getInstance()->mode === 2 && $player->hasPermission("cores.admin")) {

            $af->set("s2x", $pos->getX() + 0.5);
            $af->set("s2y", $pos->getY() + 1);
            $af->set("s2z", $pos->getZ() + 0.5);
            $af->save();

            $player->sendMessage(Main::getPrefix() . "Now the right red core§8.");
            Main::getInstance()->mode++;
            $event->cancel();

        } else if (Main::getInstance()->mode === 3 && $player->hasPermission("cores.admin")) {

            $af->set("sr1x", $pos->getX());
            $af->set("sr1y", $pos->getY());
            $af->set("sr1z", $pos->getZ());
            $af->save();

            $player->sendMessage(Main::getPrefix() . "Now the left red core§8.");
            Main::getInstance()->mode++;
            $event->cancel();

        } else if (Main::getInstance()->mode === 4 && $player->hasPermission("cores.admin")) {

            $af->set("sr2x", $pos->getX());
            $af->set("sr2y", $pos->getY());
            $af->set("sr2z", $pos->getZ());
            $af->save();

            $player->sendMessage(Main::getPrefix() . "Now the right blue core§8.");
            Main::getInstance()->mode++;
            $event->cancel();

        } else if (Main::getInstance()->mode === 5 && $player->hasPermission("cores.admin")) {

            $af->set("sb1x", $pos->getX());
            $af->set("sb1y", $pos->getY());
            $af->set("sb1z", $pos->getZ());
            $af->save();

            $player->sendMessage(Main::getPrefix() . "Now the left blue core§8.");
            Main::getInstance()->mode++;
            $event->cancel();

        } else if (Main::getInstance()->mode === 6 && $player->hasPermission("cores.admin")) {

            $af->set("sb2x", $pos->getX());
            $af->set("sb2y", $pos->getY());
            $af->set("sb2z", $pos->getZ());
            $af->save();

            $player->sendMessage(Main::getPrefix() . "Setup finished...");
            Main::getInstance()->mode = 0;

            Main::getCoresAPI()->copymap(Server::getInstance()->getDataPath() . "/worlds/" . $player->getWorld()->getFolderName(), Main::getInstance()->getDataFolder() . "/maps/" . $player->getWorld()->getFolderName());
            $spawn = Server::getInstance()->getWorldManager()->getDefaultWorld()->getSafeSpawn();
            Server::getInstance()->getWorldManager()->getDefaultWorld()->loadChunk($spawn->getX(), $spawn->getZ());
            $player->teleport($spawn, 0, 0);
            $player->setGamemode(GameMode::SURVIVAL());
            $event->cancel();

        }
    }
}
