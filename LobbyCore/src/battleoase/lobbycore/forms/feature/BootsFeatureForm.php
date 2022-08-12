<?php


namespace battleoase\lobbycore\forms\feature;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\BattlePlayer;
use battleoase\lobbycore\forms\FeatureForm;
use BattleOase\LobbyCore\LobbyCore;
use battleoase\lobbycore\utils\FeatureUtils;
use ceepkev77\cloudbridge\objects\GameServer;
use ceepkev77\lobbyapi\LobbyAPI;
use Frago9876543210\EasyForms\forms\ModalForm;
use pocketmine\network\mcpe\protocol\TransferPacket;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use Frago9876543210\EasyForms\elements\Button;
use Frago9876543210\EasyForms\forms\MenuForm;

class BootsFeatureForm
{
	public Player $player;

	private array $bootsArray = [
		"boots.rainbow" => 1000,
		"boots.gold" => 1200,
		"§cDeactivate feature" => 0
	];

	public function __construct(Player $player)
	{
		$this->player = $player;
	}

	public function open()
	{
		$buttons = [];
		foreach ($this->bootsArray as $itemName => $price) {
			if($itemName == "§cDeactivate feature") {
				$buttons[] = new Button($itemName);
			} else {
				if (FeatureUtils::hasBuyItem($itemName, $this->player->getName())) {
					$buttons[] = new Button("§a• §a§l" . $itemName . "§r§f");
				}else{
					$buttons[] = new Button("§c• §c§l" . $itemName . "§r§f" . PHP_EOL . "§8| §e" . $price);
				}
			}
		}

		$this->player->sendForm(new MenuForm(
			"§8• §dBoots§7-§dFeature §r§8•", "§7There are §e" . count($buttons) -1 . " §dBoots §7available!", $buttons,
			function(Player $player, Button $selected): void {
				$str = explode("\n", $selected->getText())[0];
				$cls = str_replace("•", "",$str);
				$featureName = TextFormat::clean($cls);

				if ($featureName === "Deactivate feature"){
					if (FeatureUtils::getFeature($player->getName())["item"] == "NULL") {

					} else {

						$player->sendMessage(BattleCore::getInstance()->languageSystem->translate($player, "LobbyPlayer.feature.deactivated", [
							"{FEATURE}" => BattleCore::getInstance()->languageSystem->translate($player, FeatureUtils::getFeature($player->getName())["item"])
						]));
						FeatureUtils::setItemFeature("NULL", $player->getName());
					}
				}else{
					if (FeatureUtils::hasBuyItem($featureName, $player->getName())){
						if (FeatureUtils::getFeature($player->getName())["item"] == $featureName) {
							FeatureUtils::setItemFeature("NULL", $player->getName());
							$player->sendMessage(BattleCore::getInstance()->languageSystem->translate($player, "LobbyPlayer.feature.deactivated", [
								"{FEATURE}" => BattleCore::getInstance()->languageSystem->translate($player, $featureName)
							]));
						} else {
							FeatureUtils::setItemFeature($featureName, $player->getName());
							$player->sendMessage(LobbyCore::PREFIX.BattleCore::getInstance()->languageSystem->translate($player, "LobbyPlayer.feature.activated", [
								"{FEATURE}" => $featureName
							]));
						}
					}else {
						$this->player->removeCurrentWindow();
						foreach ($this->bootsArray as $boots => $price) {
							$this->player->sendForm(new ModalForm("Buy Confirm", "§6Bist du dir ganz sicher das du dir das Feature " . $featureName . " kaufen willst um $price$",
								function(Player $player, bool $response) use ($price, $featureName): void {
									if ($player instanceof BattlePlayer) {
										$playerCoins = LobbyCore::getInstance()->getCoins($player);
										if ($response) {
											if ($playerCoins >= $price) {
												$player->sendMessage(LobbyCore::PREFIX . "§aVielen Dank für den Kauf!");
												FeatureUtils::setBuyItem($featureName, $player, $price);
											} else {
												$player->sendMessage(LobbyCore::PREFIX . "§cDu hast nicht genug Coins!");
											}
										}
									}
								}
							));
						}
					}
				}
			}
		));
	}
}