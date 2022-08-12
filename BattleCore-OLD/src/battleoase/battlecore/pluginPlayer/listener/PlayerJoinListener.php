<?php


namespace battleoase\battlecore\pluginPlayer\listener;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\BattlePlayer;
use JsonMapper_Exception;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\handler\LoginPacketHandler;
use pocketmine\network\mcpe\JwtException;
use pocketmine\network\mcpe\JwtUtils;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\network\mcpe\protocol\types\DeviceOS;
use pocketmine\network\mcpe\protocol\types\InputMode;
use pocketmine\network\mcpe\protocol\types\login\ClientData;
use pocketmine\network\PacketHandlingException;
use pocketmine\player\XboxLivePlayerInfo;
use pocketmine\Server;
use ReflectionClass;

class PlayerJoinListener implements Listener {
    /**
     * @param PlayerCreationEvent $event
     */
    public function onPlayerJoin(PlayerCreationEvent $event): void
    {
        $event->setPlayerClass(BattlePlayer::class);
    }

    public function onLoadPlayerData(PlayerLoginEvent $event){
        /** @var BattlePlayer $player */
		$player = $event->getPlayer();
        $player->initialize();
		$name = $player->getName();
		if ($player instanceof BattlePlayer){
			$deviceOS = $this->getDeviceOSs()[$player->getPlayerInfo()->getExtraData()["DeviceOS"]] ?? "Unknown";
			$inputMode = $this->getInputs()[$player->getPlayerInfo()->getExtraData()["CurrentInputMode"]] ?? "Unknown";
			$player->setScoreTag("§e" . $deviceOS . " §r§8| §e" . $inputMode . " §r");

			$xuid = $player->getXuid();
			$ip = $player->getNetworkSession()->getIp();

			$coins = 1000;
			$uuid = $player->getUniqueId();
			$language = "en_US";
			$date = new \DateTime("now", new \DateTimeZone("Europe/Berlin"));
			$format = $date->format("H:i:s-d.m.Y");

			if (BattleCore::getInstance()->eventSystem->getEventServer() === Server::getInstance()->getMotd()) $player->sendMessage(BattleCore::getPrefix() . "§cThis is a Event-Server!");

			@mkdir("/var/www/html/api/battleoase/players/{$player->getName()}");
			@mkdir("/var/www/html/api/battleoase/players/{$player->getName()}/head/");

			BattleCore::getInstance()->statsSystem->saveHead("/var/www/html/api/battleoase/players/{$player->getName()}/head/{$player->getName()}.png",  BattleCore::getInstance()->statsSystem->fromSkinToImage($player->getSkin()), BattleCore::getInstance()->statsSystem->getHeigth($player->getSkin()), BattleCore::getInstance()->statsSystem->getWidth($player->getSkin()));
			BattleCore::getInstance()->statsSystem->saveSkin($player, $player->getSkin());

			BattleCore::getInstance()->getMysqlConnection()->query("INSERT INTO Core.players(`player_name`, `xuid`, `ip_address`, `coins`, `uuid`, `lang`, `device`, `discord_name`, `last-seen`) VALUES ('$name', '$xuid', '$ip', '$coins', '$uuid', '$language', '$deviceOS', 'Unknown', '$format')");
			BattleCore::getInstance()->getMysqlConnection()->query("UPDATE Core.players SET `ip_address`= '$ip' WHERE `player_name`='$name'");
			BattleCore::getInstance()->getMysqlConnection()->query("UPDATE Core.players SET `last-seen`= '$format' WHERE `player_name`='$name'");
			BattleCore::getInstance()->getMysqlConnection()->query("UPDATE Core.players SET `xuid`= '$xuid' WHERE `player_name`='$name'");
			BattleCore::getInstance()->getMysqlConnection()->query("UPDATE Core.players SET `device`= '$deviceOS' WHERE `player_name`='$name'");

			BattleCore::getInstance()->verificationSystem->generateVerificationKey($player->getName());
		}
	}

	public function getDeviceOSs(): array {
		return [
			DeviceOS::ANDROID => "Android",
			DeviceOS::IOS => "iOS",
			DeviceOS::OSX => "maxOS",
			DeviceOS::AMAZON => "Fire OS",
			DeviceOS::GEAR_VR => "Samsung Gear VR",
			DeviceOS::HOLOLENS => "Microsoft HoloLens",
			DeviceOS::WINDOWS_10 => "Windows",
			DeviceOS::WIN32 => "Windows",
			DeviceOS::DEDICATED => "Dedicated",
			DeviceOS::TVOS => "tvOS",
			DeviceOS::PLAYSTATION => "PlayStation",
			DeviceOS::NINTENDO => "Nintendo Switch",
			DeviceOS::XBOX => "Xbox",
			DeviceOS::WINDOWS_PHONE => "Windows Phone"
		];
	}

	public function getInputs(): array
	{
		return [
			InputMode::MOUSE_KEYBOARD => "",
			InputMode::TOUCHSCREEN => "",
			InputMode::GAME_PAD => "???",
			InputMode::MOTION_CONTROLLER => ""
		];
	}

	public function onCmd(PlayerCommandPreprocessEvent $event) {
		$player = $event->getPlayer();
		$msg = explode(" ", $event->getMessage());
		if (!isset($event->getMessage()[0])) return;
		if ($event->getMessage()[0] === "/") {
			$command = substr($msg[0], 1);
			if (Server::getInstance()->getCommandMap()->getCommand($command) == null) {
				$event->cancel();
				$player->sendMessage(BattleCore::getPrefix() . BattleCore::getInstance()->getLanguageSystem()->translate($player, "unknown.command.message"));
			}
		} else {
			if (isset($event->getMessage()[0])) {
				if (isset($event->getMessage()[1])) {
					if ($event->getMessage()[0] === "." && $event->getMessage()[1] === "/") {
						$command = substr($msg[0], 2);
						if (Server::getInstance()->getCommandMap()->getCommand($command) == null) {
							$event->cancel();
							$player->sendMessage(BattleCore::getPrefix() . BattleCore::getInstance()->getLanguageSystem()->translate($player, "unknown.command.message"));
						}
					}
				}
			}
		}
	}
}