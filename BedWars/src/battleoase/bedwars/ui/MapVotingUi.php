<?php


namespace battleoase\bedwars\ui;


use battleoase\battlecore\BattlePlayer;
use battleoase\bedwars\BedWars;
use battleoase\bedwars\caches\MapCache;
use Closure;
use Frago9876543210\EasyForms\elements\Button;
use Frago9876543210\EasyForms\forms\MenuForm;
use pocketmine\network\mcpe\protocol\TransferPacket;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class MapVotingUi
{
	public function __construct(protected Player $player) {}

	public function open(){
		$buttons = [];
		foreach (MapCache::$map as $name => $item){
			$buttons[] = new Button("§e".$name . PHP_EOL . TextFormat::YELLOW . MapCache::$votes[$name]);
		}
		$this->player->sendForm(new MenuForm(
			"§§8● §7Map Voting", "§7Only 1 Vote",
			$buttons,
			function (Player $player, Button $button): void {
				if (!BedWars::getInstance()->getPlayerManager()->getPlayer($player)->hasMapVoted()){
					$str = explode("\n", $button->getText())[0];
					$map = TextFormat::clean($str);
					MapCache::$votes[$map] = MapCache::$votes[$map] +1;
					BedWars::getInstance()->getPlayerManager()->getPlayer($player)->setMapVote(true);
					$player->sendMessage(BedWars::PREFIX."§aYou have successfully voted for the§b {$map}§a map");
				}else{
					$player->sendMessage(BedWars::PREFIX."§cYou has already voted for a Map!");
				}
			}
		));
	}

}