<?php

namespace ceepkev77\lobbyapi\command;

use ceepkev77\cloudbridge\CloudBridge;
use ceepkev77\cloudbridge\network\DataPacket;
use ceepkev77\cloudbridge\network\packet\GameServerInfoRequestPacket;
use ceepkev77\cloudbridge\network\packet\GameServerInfoResponsePacket;
use ceepkev77\cloudbridge\network\packet\ListServerRequestPacket;
use ceepkev77\cloudbridge\network\packet\ListServerResponsePacket;
use ceepkev77\cloudbridge\network\packet\PlayerMovePacket;
use ceepkev77\lobbyapi\api\QuickJoinAPI;
use ceepkev77\lobbyapi\LobbyAPI;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;

class QuickJoinCommand extends Command
{

    public function __construct()
    {
        parent::__construct("quickjoin", "Verified By BattleCloud@Betav2", false, ["qj"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if(isset($args[0])) {
            if(is_dir("/home/cloud/templates/" . $args[0])) {
                if($sender instanceof Player) {
                    QuickJoinAPI::sendQuickJoinForm($sender, $args[0]);
                }
            } else {
                $sender->sendMessage(CloudBridge::getPrefix() . "§7Template not exist!");
            }
        } else {
            $sender->sendMessage(CloudBridge::getPrefix() . "§eSyntax: §7/qj <template>");
        }
    }

}