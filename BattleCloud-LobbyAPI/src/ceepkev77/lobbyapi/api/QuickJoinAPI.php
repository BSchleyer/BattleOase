<?php

namespace ceepkev77\lobbyapi\api;

use ceepkev77\cloudbridge\CloudBridge;
use ceepkev77\cloudbridge\network\DataPacket;
use ceepkev77\cloudbridge\network\packet\ListServerRequestPacket;
use ceepkev77\cloudbridge\network\packet\ListServerResponsePacket;
use ceepkev77\cloudbridge\objects\GameServerState;
use ceepkev77\lobbyapi\LobbyAPI;
use Frago9876543210\EasyForms\elements\Button;
use Frago9876543210\EasyForms\forms\MenuForm;
use pocketmine\player\Player;

class QuickJoinAPI
{

    public const TELEPORT = [
        "BW-2x1" => "0:0:0",
    ];

    public static function sendQuickJoinForm(Player $player, String $quickTemplate): void
    {
        $buttons = [new Button("§7QuickJoin")];
        if(isset(self::TELEPORT[$quickTemplate])) {
            $buttons[] = new Button("§7Teleport");
        }

        $player->sendForm(new MenuForm(
            "§e§lQuickJoin",
            "",
            $buttons,
            function (Player $player, Button $button) use ($quickTemplate): void {
                switch ((int)$button->getValue()) {
                    case 0:
                        $listServer = new ListServerRequestPacket();
                        $listServer->submitRequest($listServer, function (DataPacket $dataPacket) use ($quickTemplate, $player) {
                            if($dataPacket instanceof ListServerResponsePacket) {
                                $servers = json_decode($dataPacket->data["servers"], true);
                                $onlineServers = [];
                                foreach ($servers as $server) {
                                    $template = explode("-", $server)[0];
                                    if(str_contains($quickTemplate, $template)) {
                                        if(LobbyAPI::getGameServerProvider()->getGameServer($server)->getState() == GameServerState::INGAME) {

                                        } else {
                                            if(LobbyAPI::getGameServerProvider()->getGameServer($server)->getPlayerCount() >= LobbyAPI::getGameServerProvider()->getGameServer($server)->getCloudGroup()->getMaxPlayer()) {

                                            } else {
                                                $onlineServers[LobbyAPI::getGameServerProvider()->getGameServer($server)->getName()] = LobbyAPI::getGameServerProvider()->getGameServer($server)->getPlayerCount();
                                            }

                                        }

                                    }

                                }
                                if(count($onlineServers) == 0 ) {
                                    $player->sendMessage(CloudBridge::getPrefix() . "§cKein freier server gefunden!");
                                } else {
                                    $topServer = array_search(max($onlineServers),$onlineServers);
                                    $player->sendMessage(CloudBridge::getPrefix() . "§7Teleporting to " . $topServer . "§7...");
                                    $player->transfer($topServer);
                                }

                            }
                        });
                        break;
                    case 1:
                        $player->sendMessage(QuickJoinAPI::TELEPORT[$quickTemplate]);
                        break;
                }
            }
        ));
    }

}