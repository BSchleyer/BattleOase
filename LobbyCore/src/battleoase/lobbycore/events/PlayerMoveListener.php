<?php


namespace battleoase\lobbycore\events;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\BattlePlayer;
use battleoase\lobbycore\eventSystem\EventSystem;
use battleoase\lobbycore\LobbyCore;
use battleoase\lobbycore\utils\SettingUtils;
use ceepkev77\communicationsystem\packets\PlayerMovePacket;
use pocketmine\entity\Attribute;
use pocketmine\entity\AttributeMap;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityMotionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJumpEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerToggleFlightEvent;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\TransferPacket;
use pocketmine\scheduler\ClosureTask;
use pocketmine\world\particle\FlameParticle;
use pocketmine\world\Position;
use pocketmine\world\sound\FireExtinguishSound;

class PlayerMoveListener implements Listener
{
	public static array $blocks = [];
	public array $prot = [];
	public array $doublejumpcache = [];

	public function onMove(PlayerMoveEvent $event){
		$player = $event->getPlayer();
		$y = $player->getPosition()->getY() + 1;
		if ($player->getLocation()->getY() <= -35) {
			$player->teleport(new Vector3(-42752, 49, -5888));
		}

		if ($player instanceof BattlePlayer) {
			if ($player->getWorld()->getBlock(new Position($player->getPosition()->getX(), $y, $player->getPosition()->getZ(), $player->getWorld()))->getId() === 90) {
				if (!$player->hasCooldown("EVENT_JOIN")) {
					if (BattleCore::getInstance()->eventSystem->getEventServer() == null) {
						$player->sendMessage(BattleCore::getInstance()->eventSystem->prefix . BattleCore::getInstance()->getLanguageSystem()->translate($player, "event.message.offline"));
					} else {
						$pk = new TransferPacket();
						$server = BattleCore::getInstance()->eventSystem->getEventServer();
						//$pk->address = $server["eventServer"];
						var_dump($server);
						//$event->getPlayer()->getNetworkSession()->sendDataPacket($pk);
					}
					$player->resetCooldown("EVENT_JOIN", 20 * 5);
				}

			}
		}
	}

	public function onDoubleJump(PlayerToggleFlightEvent $event)
	{
		$player = $event->getPlayer();
		if ($player instanceof BattlePlayer){
			if ($player->isSurvival()) {
				if (SettingUtils::get($event->getPlayer()->getName())["doubleJump"]) {
						$player->setAllowFlight(false);
						$player->setFlying(false);

						$player->knockBack($player->getDirectionVector()->getX(), $player->getDirectionVector()->getZ(), 1.3, 1.5);
						$player->getWorld()->addSound(new Vector3($player->getPosition()->getX(), $player->getPosition()->getY(), $player->getPosition()->getZ()), new FireExtinguishSound());
						LobbyCore::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($player): void {
							if ($player->isConnected()){
								if (SettingUtils::get($player->getName())["doubleJump"]) {
									$player->setAllowFlight(true);
									$player->setFlying(false);
								}
							}
						}), 60);
					}
				}
		}
	}
}