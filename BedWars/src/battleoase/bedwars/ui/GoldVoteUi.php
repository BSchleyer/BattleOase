<?php


namespace battleoase\bedwars\ui;


use battleoase\bedwars\BedWars;
use battleoase\bedwars\caches\MapCache;
use Frago9876543210\EasyForms\elements\Button;
use Frago9876543210\EasyForms\forms\MenuForm;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class GoldVoteUi
{
	public function __construct(protected Player $player) {}

	public function open(){
		$buttons = [];
		foreach (MapCache::$map as $name => $item){
			$buttons[] = new Button("§aFor §eGold". PHP_EOL . TextFormat::YELLOW . MapCache::$goldVotes[$name]);
			$buttons[] = new Button("§cAgainst §eGold" . PHP_EOL . TextFormat::YELLOW . MapCache::$goldVotes[$name]);
		}
		$this->player->sendForm(new MenuForm(
			"§8● 7Gold Voting", "§7Only 1 Vote",
			$buttons,
			function (Player $player, Button $button): void {
				if (!BedWars::getInstance()->getPlayerManager()->getPlayer($player)->hasGoldVoted()){
					$str = explode("\n", $button->getText())[0];
					$gold = TextFormat::clean($str);
					MapCache::$goldVotes[$gold] = MapCache::$goldVotes[$gold] +1;
					BedWars::getInstance()->getPlayerManager()->getPlayer($player)->setGoldVoted(true);
					$player->sendMessage(BedWars::PREFIX."§aYou have successfully voted for gold!");
				}else{
					$player->sendMessage(BedWars::PREFIX."§cYou has already voted for a Map!");
				}
			}
		));
	}
}