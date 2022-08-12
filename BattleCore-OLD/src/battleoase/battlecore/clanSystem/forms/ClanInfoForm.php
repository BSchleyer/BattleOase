<?php


namespace battleoase\battlecore\clanSystem\forms;


use battleoase\battlecore\clanSystem\api\ClanAPI;
use battleoase\battlecore\clanSystem\ClanSystem;
use Frago9876543210\EasyForms\elements\Button;
use Frago9876543210\EasyForms\forms\MenuForm;
use pocketmine\player\Player;
use pocketmine\Server;

class ClanInfoForm extends MenuForm {

	public function __construct($clan) {
		$clanData = ClanAPI::getClan($clan);
		$clanColor = ClanSystem::COLORS[$clanData->getColor()];
		$clanState = ClanSystem::stateIntToString($clanData->getState());
		$clanInfo = str_replace("-", " │ ", $clanData->getCreatedAt());

		$data = [$clanData->getCreatedAt(), $clanData->getClanTag(), $clanData->getOwner(), $clanData->getColor(), $clanData->getState(), $clanData->getElo(), $clanData->getCustomInfo(), $clanData->getLosesCw(), $clanData->getWinsCw()];
		parent::__construct(
			$clanColor . $clan,
			"§eList Informations about the $clan Clan\n\n§7Clan-Name » ".$clanColor.$clan."\n§7Clan-Tag » [".$clanColor.$data[1]."§7]\n§7Created at » §7$clanInfo\n\n§7Leader » §c$data[2]\n§7Elo » §r§f$data[5]\n§7State » §c$clanState\n\n§7About » §r§f$data[6]\n\n§c§lLoses §r§f§7| §7ClanWars » §c$data[7]\n§a§lWins §r§f§7| §7ClanWars » §c$data[8]",
			[
				new Button("Ok")
			], function(Player $player, Button $button): void {
			}
		);
	}

}