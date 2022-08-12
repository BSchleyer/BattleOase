<?php


namespace battleoase\battlecore\clanWarsQueueSystem;


use battleoase\battlecore\clanSystem\api\PlayerClanAPI;
use battleoase\battlecore\clanWarsQueueSystem\commands\ClanWarsCommand;
use battleoase\battlecore\utils\BPlugin;
use ceepkev77\cloudbridge\network\packet\AddPlayerToCWQueuePacket;
use Frago9876543210\EasyForms\elements\Dropdown;
use Frago9876543210\EasyForms\forms\CustomForm;
use Frago9876543210\EasyForms\forms\CustomFormResponse;
use pocketmine\player\Player;
use pocketmine\Server;

class ClanWarsQueueSystem extends BPlugin
{

    public function __construct()
    {
        Server::getInstance()->getCommandMap()->register("clanwars", new ClanWarsCommand());
    }


	public static function sendQueue(Player $player, $count = 4) {
		if (($clan = PlayerClanAPI::getPlayersClanData($player)) != null) {
			$buttons = [];
			$clanName = $clan->getClanName();
			if(count(PlayerClanAPI::getPlayersInClan($clan->getClanName())) >= $count) {
				for ($i = 1; $i <= $count; $i++) {
					$buttons[] = new Dropdown("Player " . $i, PlayerClanAPI::getPlayersInClan($clan->getClanName()));
				}
				$player->sendForm(new CustomForm("§a§lClanWars", $buttons,
					function(Player $player, CustomFormResponse $response) use ($count, $clanName) : void{
						foreach ($response->getValues() as $value) {
							$player->sendMessage("You selected: ". $value);
							$packet = new AddPlayerToCWQueuePacket();
							$packet->playerName = $value;
							$packet->clanName = $clanName;
							$packet->sendPacket();
						}
					}));
			} else {
				$player->sendMessage("§cZu wenig spieler im clan" . count(PlayerClanAPI::getPlayersInClan($clan->getClanName())));
			}

		} else {
			$player->sendMessage("§ckein clan");
		}
	}
}