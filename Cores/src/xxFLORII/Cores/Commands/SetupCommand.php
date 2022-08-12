<?php

namespace xxFLORII\Cores\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\World;
use xxFLORII\Cores\Main;

class SetupCommand extends Command {

    public function __construct()
    {
        parent::__construct("setup", "Start Command", "/setup", ["cores"]);
        $this->setPermission("cores.admin");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player){
            $sender->sendMessage("§cYou cannot use this command in the console.");
            return;
        }

        if (!$this->testPermissionSilent($sender)){
            $sender->sendMessage("§cYou don't have enough permissions to execute this command.");
            return;
        }

        if (isset($args[0])) {
            if (is_dir(Server::getInstance()->getDataPath() . "/worlds/" . $args[0])) {
                if (!Server::getInstance()->getWorldManager()->getWorldByName($args[0]) instanceof World) {
                    Server::getInstance()->getWorldManager()->loadWorld($args[0], true);
                }

                $cfg = Main::getInstance()->getConfig();
                $cfg->set("Arena", $args[0]);
                $cfg->save();

                $spawn = Server::getInstance()->getWorldManager()->getWorldByName($args[0])->getSafeSpawn();
                Server::getInstance()->getWorldManager()->getWorldByName($args[0])->loadChunk($spawn->getX(), $spawn->getZ());
                $sender->teleport($spawn, 0, 0);
                $sender->sendMessage(Main::getPrefix() . "§aSelected arena §d{$args[0]}§8. §eNow tap the spawn for the red player§8.");
                $sender->setGamemode(GameMode::CREATIVE());
                Main::getInstance()->mode++;
            }
        }
    }
}