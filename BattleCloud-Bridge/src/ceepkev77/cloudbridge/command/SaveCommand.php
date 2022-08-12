<?php


namespace ceepkev77\cloudbridge\command;

use ceepkev77\cloudbridge\CloudBridge;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\CommandException;
use pocketmine\Server;
use pocketmine\utils\MainLogger;

class SaveCommand extends Command
{

    public function __construct()
    {
        parent::__construct("save", "Save GameServer");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        $template = CloudBridge::getInstance()->getTemplate();
        $serverName = CloudBridge::getInstance()->getServer()->getMotd();
        var_dump($template);
        var_dump($serverName);

        if ($sender->hasPermission("admin")){
			Server::getInstance()->getCommandMap()->dispatch($sender, "save-all");

			$path1 = "/home/cloud/temp/". $serverName ."/";
			$path = "/home/cloud/";

			Server::getInstance()->getLogger()->info("§aSave server§e " . $serverName);
			if (is_dir("{$path}templates/") && is_dir($path1)) {
				passthru("rm -r {$path}templates/" . $template . "/worlds/");
				passthru("mkdir {$path}templates/" . $template . "/worlds/");
				passthru("cp -r " . $path1 . "worlds/* {$path}templates/" . $template . "/worlds/");
                passthru("cp -r " . $path1 . "plugin_data/* {$path}templates/" . $template . "/plugin_data/");
				$sender->sendMessage(CloudBridge::getPrefix()."§r§f§aThe Template is now updated!");
			} else {
				$sender->sendMessage("§b§lCloud §8x §r§f§cError whilst saving files§7!§c Folder don't exists§7!");
			}
		}
    }
}