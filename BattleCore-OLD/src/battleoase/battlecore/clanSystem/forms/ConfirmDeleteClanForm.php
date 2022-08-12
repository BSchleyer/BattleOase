<?php


namespace battleoase\battlecore\clanSystem\forms;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\clanSystem\api\ClanAPI;
use battleoase\battlecore\clanSystem\api\PlayerClanAPI;
use battleoase\battlecore\clanSystem\ClanSystem;
use ceepkev77\cloudbridge\network\DataPacket;
use ceepkev77\cloudbridge\network\packet\PlayerMessagePacket;
use Frago9876543210\EasyForms\elements\Button;
use Frago9876543210\EasyForms\forms\MenuForm;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat as c;

class ConfirmDeleteClanForm extends MenuForm {

	public Player $player;

	/**
	 * ConfirmDeleteClanForm constructor.
	 * @param Player $player
	 */
	public function __construct(Player $player) {
		parent::__construct(
			ClanSystem::PREFIX,
			BattleCore::translate($player, "ClanSystem.confirm.deleteClan"),
			[
				new Button("§aConfirm ✔"),
				new Button("§cCancel ✘")
			], function(Player $player, Button $button): void {
				switch ($button->getValue()){
					case 0:
						if (($data = PlayerClanAPI::getPlayersClanData($player))->getRank() == ClanSystem::LEADER) {
							foreach (PlayerClanAPI::getPlayersInClan($data->getClanName()) as $p_name) {
								PlayerClanAPI::unsetPlayersClan($p_name);
								$playerExact = Server::getInstance()->getPlayerExact($p_name);
								if ($playerExact->isOnline() && $playerExact->isConnected()){
									$playerExact->sendMessage(ClanSystem::PREFIX . "§cYou have been removed from the Clan. The Clan was deleted.");
								}else{
									if (!$playerExact->isOnline()){
										$pk = new PlayerMessagePacket();
										$pk->playerName = $playerExact->getName();
										$pk->value = ClanSystem::PREFIX . "§cYou have been removed from the Clan. The Clan was deleted.";
										$pk->sendPacket();
									}
								}
							}
							ClanAPI::removeClan($data->getClanName());
							$player->sendMessage(ClanSystem::PREFIX.c::RED."You deleted the clan!");
						} else {
							$player->sendMessage(ClanSystem::PREFIX.c::RED."To delete the clan you must be the Leader");
						}
						break;
					case 1:

						$player->sendForm(new MainClanForm($player));
				}
			}
		);
		$this->player = $player;
	}
}