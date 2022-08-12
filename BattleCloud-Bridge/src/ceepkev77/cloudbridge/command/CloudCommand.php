<?php

namespace ceepkev77\cloudbridge\command;

use ceepkev77\cloudbridge\network\DataPacket;
use ceepkev77\cloudbridge\network\packet\AddPlayerToCWQueuePacket;
use ceepkev77\cloudbridge\network\packet\ListCloudPlayersRequestPacket;
use ceepkev77\cloudbridge\network\packet\ListCloudPlayersResponsePacket;
use ceepkev77\cloudbridge\network\packet\StartGroupPacket;
use ceepkev77\cloudbridge\network\packet\StartServerPacket;
use ceepkev77\cloudbridge\network\packet\StopGroupPacket;
use ceepkev77\cloudbridge\network\packet\StopServerPacket;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\KnownTranslationFactory;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;

class CloudCommand extends Command
{

    public function __construct()
    {
        parent::__construct("cloud", "BattleCloud", false, ["cl", "bc", "battlecloud"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            if ($sender->hasPermission("admin")) {
                if (isset($args[0])) {
                    if ($args[0] == "startserver") {
                        if (isset($args[1]) && isset($args[2])) {
                            $group = $args[1];
                            $count = $args[2];
                            $pk = new StartServerPacket();
                            $pk->addValue("groupName", $group);
                            $pk->addValue("count", $count);
                            $pk->sendPacket();
                            $sender->sendMessage("§aPacket wurde zur Cloud gesendet!");
                        } else {
                            $sender->sendMessage("/cloud startserver <group> <count>");
                        }
                    } elseif ($args[0] == "stopserver") {
                        if (isset($args[1])) {
                            $server = $args[1];
                            $pk = new StopServerPacket();
                            $pk->addValue("serverName", $server);
                            $pk->sendPacket();
                            $sender->sendMessage("§aPacket wurde zur Cloud gesendet!");
                        } else {
                            $sender->sendMessage("/cloud stopserver <serverName>");
                        }
                    } elseif ($args[0] == "groupstop") {
                        if (isset($args[1])) {
                            $group = $args[1];
                            $pk = new StopGroupPacket();
                            $pk->addValue("groupName", $group);
                            $pk->sendPacket();
                            $sender->sendMessage("§aPacket wurde zur Cloud gesendet!");
                        } else {
                            $sender->sendMessage("/cloud groupstop <serverName>");
                        }
                    } elseif ($args[0] == "groupstart") {
                        if (isset($args[1])) {
                            $group = $args[1];
                            $pk = new StartGroupPacket();
                            $pk->addValue("groupName", $group);
                            $pk->sendPacket();
                            $sender->sendMessage("§aPacket wurde zur Cloud gesendet!");
                        } else {
                            $sender->sendMessage("/cloud groupstart <serverName>");
                        }
                    } elseif ($args[0] == "list") {
                        $pk = new ListCloudPlayersRequestPacket();
                        $pk->submitRequest($pk, function (DataPacket $dataPacket) use ($sender) {
                            if($dataPacket instanceof ListCloudPlayersResponsePacket) {
                                $playerNames = $dataPacket->players;
                                sort($playerNames, SORT_STRING);
                                $sender->sendMessage("Es sind " . count($playerNames) . "/100 Spieler online:");
                                $sender->sendMessage(implode(", ", $playerNames));
                            }
                        });
                    } elseif ($args[0] == "add") {
                        if (isset($args[1]) && isset($args[2]) ) {
                            $clanName = $args[1];
                            $playerName = $args[2];
                            $pk = new AddPlayerToCWQueuePacket();
                            $pk->playerName = $playerName;
                            $pk->clanName = $clanName;
                            $pk->sendPacket();
                            $sender->sendMessage("§aPacket wurde zur Cloud gesendet!");
                        } else {
                            $sender->sendMessage("/cloud add <clanName> <playerName>");
                        }
                    }
                } else {
                    $sender->sendMessage("/cloud < startserver | stopserver | groupstop | groupstart | transfer | list>");
                }
            } else {
                $sender->sendMessage("§cNo Perms");
            }
        }
    }

}