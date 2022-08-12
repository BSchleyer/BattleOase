<?php


namespace battleoase\lobbycore\commands;


use battleoase\battlecore\BattleCore;
use battleoase\lobbycore\LobbyCore;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\world\sound\BlazeShootSound;
use pocketmine\world\sound\ExplodeSound;
use pocketmine\world\sound\FizzSound;
use pocketmine\world\sound\TotemUseSound;
use pocketmine\world\sound\XpCollectSound;
use pocketmine\world\sound\XpLevelUpSound;

class FlyCommand extends Command
{

	public function __construct()
	{
		$this->setPermission("fly.command");
		parent ::__construct("fly", "Fly Command", "/fly");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if ($sender->hasPermission($this->getPermission())){
			if ($sender instanceof Player){
				if ($sender->getGamemode() !== GameMode::CREATIVE()){
					if (!$sender->isFlying()){
						$sender->setAllowFlight(true);
						$sender->setFlying(true);
						$sender->sendTitle(BattleCore::getInstance()->getLanguageSystem()->translate($sender, "lobby.fly.enable"));
						$sender->getWorld()->addSound(new Vector3($sender->getPosition()->getX(), $sender->getPosition()->getY(), $sender->getPosition()->getZ()), new TotemUseSound());
					}else{
						$sender->setFlying(false);
						$sender->setAllowFlight(false);
						$sender->sendTitle(BattleCore::getInstance()->getLanguageSystem()->translate($sender, "lobby.fly.disable"));
						$sender->getWorld()->addSound(new Vector3($sender->getPosition()->getX(), $sender->getPosition()->getY(), $sender->getPosition()->getZ()), new BlazeShootSound());
					}
				}else{
					$sender->sendMessage(BattleCore::getPrefix() . "§cYou are in Gamemode!");
				}
			}
		}
	}
}