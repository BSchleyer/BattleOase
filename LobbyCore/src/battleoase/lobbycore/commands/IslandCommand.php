<?php


namespace battleoase\lobbycore\commands;


use battleoase\lobbycore\forms\TeleporterForm;
use Frago9876543210\EasyForms\elements\Button;
use Frago9876543210\EasyForms\forms\MenuForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\Server;

class IslandCommand extends Command
{

	public const GAMES = [
		"bedwars" => ["BW-2x1", "BW-8x1"],
		"ffa" => ["BuildFFA", "FFA"],
		"lobby" => ["Lobby"],
		"gungame" => ["GunGame"],
		"training" => ["Training"],
		"cores" => ["Cores-2x4"]
	];

	public function __construct()
	{
		parent::__construct("island", "Island Command", "/island", ["games"]);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		$teleportForm = new TeleporterForm($sender);
		if ($sender instanceof Player) {
			if (isset($args[0])){
				if(isset(self::GAMES[$args[0]])) {
					if(count(self::GAMES[$args[0]]) == 1) {
						Server::getInstance()->dispatchCommand($sender, "qj " . self::GAMES[$args[0]][0]);
					} else {
						$sender->sendForm(new MenuForm(
							$args[0],
							"",
							self::GAMES[$args[0]],
							function (Player $player, Button $button): void {
								Server::getInstance()->dispatchCommand($player, "qj " . $button->getText());
							}
						));
					}
				}
			}else{
				$teleportForm->open();
			}
		}
	}
}