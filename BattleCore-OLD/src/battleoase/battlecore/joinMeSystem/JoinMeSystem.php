<?php


namespace battleoase\battlecore\joinMeSystem;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\joinMeSystem\commands\JoinMECommand;
use battleoase\battlecore\joinMeSystem\events\EventListener;
use battleoase\battlecore\joinMeSystem\utils\Utils;
use battleoase\battlecore\utils\BPlugin;
use pocketmine\network\mcpe\protocol\TransferPacket;
use pocketmine\player\Player;
use pocketmine\Server;

class JoinMeSystem extends BPlugin
{

	public static array $player;

	const PREFIX = "§4§lJoin§cME §r§f§7» §7";

    /*** @var Utils */
    private Utils $utils;

    public function __construct()
    {
        $this->utils = new Utils();

        Server::getInstance()->getCommandMap()->register("joinme", new JoinMECommand());
        Server::getInstance()->getPluginManager()->registerEvents(new EventListener(), BattleCore::getInstance());
    }

    /**
     * @return Utils
     */
    public function getUtils(): Utils
    {
        return $this->utils;
    }

	public static function transfer(Player $player, string $server)
	{
		$pk = new TransferPacket();
		$pk->address = $server;
		$player->getNetworkSession()->sendDataPacket($pk);
		return true;
	}

}
