<?php


namespace battleoase\lobbycore\bridge\events;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\BattlePlayer;
use battleoase\lobbycore\bridge\Bridge;
use battleoase\lobbycore\LobbyCore;
use battleoase\lobbycore\player\PlayerManager;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class PlayerMoveListener implements Listener
{

	private Bridge $plugin;
	public $finish;
	private bool $game = false;

	public function __construct(Bridge $plugin)
	{
		$this->plugin = $plugin;
	}


	public $bridge;

	public function onMove(PlayerMoveEvent $event) {
		$player = $event->getPlayer();
		$block = $player->getWorld()->getBlock(new Vector3($player->getPosition()->getX(), $player->getPosition()->getY() - 0.5, $player->getPosition()->getZ()));

		if ($player instanceof BattlePlayer){
			if($block->getId() == VanillaBlocks::LAPIS_LAZULI()->getId()){
				if($this->plugin->bridge[$player->getName()] == false) {
					if($this->game == false) {

						$this->plugin->bridge[$player->getName()] = true;
						$player->getInventory()->clearAll();
						/**$player->setAllowFlight(false);
						$player->setFlying(false);**/
						$player->sendMessage(LobbyCore::getInstance()->getBridge()->prefix . BattleCore::getInstance()->getLanguageSystem()->translate($player, "Bridge.message.start"));
						$item = ItemFactory::getInstance()->get(ItemIds::SANDSTONE);
						$item->setCount(64);

						$player->getInventory()->setItem(0, $item);
						$player->getInventory()->setItem(1, $item);
						$player->getInventory()->setItem(2, $item);
						$player->getInventory()->setItem(3, $item);
						$player->getInventory()->setItem(4, $item);

						$this->finish[$player->getName()] = false;
						$this->game = true;

						LobbyCore::getInstance()->getScheduler()->scheduleRepeatingTask(new class($player,$player->getPosition()->getX(), $player->getPosition()->getY(), $player->getPosition()->getZ(), $this) extends Task {

							public $player;
							public $x;
							public $y;
							public $z;
							public $old;
							public $plugin;
							public function __construct(Player $player,$x, $y, $z, PlayerMoveListener $pl)
							{
								$this->player = $player;
								$this->x = $x;
								$this->y = $y;
								$this->z = $z;
								$this->old = $y;
								$this->plugin = $pl;
							}

							public function onRun(): void
							{
								if ($this->player instanceof Player) {
									if(!$this->plugin->finish[$this->player->getName()] == true) {
										for ($i = 1; $i <= 3; $i++) {
											if($this->player->getWorld()->getBlock(new Vector3($this->x + $i, $this->old, $this->z + 32))->getId()  == VanillaBlocks::GOLD()->getId()) {
												$this->player->getWorld()->setBlock(new Vector3($this->x + $i, $this->old, $this->z + 32), VanillaBlocks::AIR() );
												$this->player->getWorld()->setBlock(new Vector3($this->x - $i, $this->old, $this->z + 32), VanillaBlocks::AIR());
												$this->player->getWorld()->setBlock(new Vector3($this->x, $this->old, $this->z + 32), VanillaBlocks::AIR());
											}

										}
										for ($i = 1; $i <= 3; $i++) {
											if (!$this->player->getPosition()->getY() - 1 <= 80) {
												if($this->player->getWorld()->getBlock(new Vector3($this->x + $i, $this->old, $this->z + 32))->getId() == VanillaBlocks::GOLD()->getId()) {
													$this->player->getWorld()->setBlock(new Vector3($this->x + $i, $this->player->getPosition()->getY(), $this->z + 32), VanillaBlocks::AIR());
													$this->player->getWorld()->setBlock(new Vector3($this->x - $i, $this->player->getPosition()->getY(), $this->z - 32), VanillaBlocks::AIR());
													$this->player->getWorld()->setBlock(new Vector3($this->x, $this->player->getPosition()->getY(), $this->z + 32), VanillaBlocks::AIR());
												}
												if($this->player->getWorld()->isInWorld($this->player->getPosition()->getX(), $this->player->getPosition()->getY(), $this->player->getPosition()->getZ())){
													$this->player->getWorld()->setBlock(new Vector3($this->x + $i, $this->player->getPosition()->getY() - 1, $this->z + 32), VanillaBlocks::GOLD());
													$this->player->getWorld()->setBlock(new Vector3($this->x - $i, $this->player->getPosition()->getY() - 1, $this->z + 32), VanillaBlocks::GOLD());
													$this->player->getWorld()->setBlock(new Vector3($this->x, $this->player->getPosition()->getY() - 1, $this->z + 32), VanillaBlocks::GOLD());
													$this->old = $this->player->getPosition()->getY() -1;
												}else{
													$this->player->sendMessage(LobbyCore::getInstance()->getBridge()->prefix . BattleCore::getInstance()->getLanguageSystem()->translate($this->player, "Bridge.reached.block.height"));
												}
											}
										}

										if ($this->player->getPosition()->getY() <= 80) {
											for ($i = 1; $i <= 3; $i++) {
												if($this->player->getWorld()->getBlock(new Vector3($this->x + $i, $this->old, $this->z + 32))->getId() == VanillaBlocks::GOLD()->getId()) {
													$this->player->getWorld()->setBlock(new Vector3($this->x + $i, $this->old, $this->z + 32), VanillaBlocks::AIR());
													$this->player->getWorld()->setBlock(new Vector3($this->x - $i, $this->old, $this->z + 32), VanillaBlocks::AIR());
													$this->player->getWorld()->setBlock(new Vector3($this->x, $this->old, $this->z + 32), VanillaBlocks::AIR());
												}
											}
											$this->getHandler()->remove();
										}
									}


									if($this->plugin->finish[$this->player->getName()] == true) {
										$this->plugin->finish[$this->player->getName()] = false;
										for ($i = 1; $i <= 3; $i++) {
											$this->player->getWorld()->setBlock(new Vector3($this->x + $i, $this->old, $this->z + 32), VanillaBlocks::AIR());
											$this->player->getWorld()->setBlock(new Vector3($this->x - $i, $this->old, $this->z + 32), VanillaBlocks::AIR());
											$this->player->getWorld()->setBlock(new Vector3($this->x, $this->old, $this->z + 32), VanillaBlocks::AIR());
										}
										$this->getHandler()->remove();

									}
								}
							}
						}, 30);
					} else {
						$player->sendMessage(LobbyCore::getInstance()->getBridge()->prefix . BattleCore::getInstance()->getLanguageSystem()->translate($player, "Bridge.message.player.isinLobbyGame"));
					}
				}


			} elseif($block->getId() == VanillaBlocks::GOLD()->getId()) {
				if($this->plugin->bridge[$player->getName()] == true) {

					$blocks = $this->plugin->blocks;
					foreach ($blocks as $block) {
						$b = explode(':', $block);
						$player->getWorld()->setBlock(new Vector3($b['0'], $b['1'], $b['2']), VanillaBlocks::AIR());
					}
					$player->sendMessage(LobbyCore::getInstance()->getBridge()->prefix . BattleCore::getInstance()->getLanguageSystem()->translate($player, "Bridge.message.finish"));
					$this->plugin->bridge[$player->getName()] = false;
					$this->finish[$event->getPlayer()->getName()] = true;
					PlayerManager::getPlayer($event->getPlayer())->giveItems();
					$rndm = mt_rand(5, 60);
					$player->addcoins($rndm);
					$this->game = false;
					$event->getPlayer()->teleport(Server::getInstance()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
				}
			} elseif($this->plugin->bridge[$player->getName()] == true) {
				if($player->getGamemode() === GameMode::CREATIVE() || $player->getGamemode() === GameMode::SPECTATOR() or $player->getAllowFlight()) {
					$player->setAllowFlight(false);
					$player->setFlying(false);
					$player->setGamemode(GameMode::SURVIVAL());
				}
			}
			if ($event->getPlayer()->getPosition()->getY() <= 80) {
				if($this->plugin->bridge[$player->getName()] == true) {
					$event->getPlayer()->teleport(Server::getInstance()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
					PlayerManager::getPlayer($event->getPlayer())->giveItems();
					$this->plugin->bridge[$player->getName()] = false;
					$this->game = false;
					$player->sendMessage(LobbyCore::getInstance()->getBridge()->prefix . BattleCore::getInstance()->getLanguageSystem()->translate($player, "Bridge.message.lose"));
					foreach (Server::getInstance()->getOnlinePlayers() as $p) {
						$player->showPlayer($p);
					}
					$blocks = $this->plugin->blocks;
					foreach ($blocks as $block) {
						$b = explode(':', $block);
						$player->getWorld()->setBlock(new Vector3($b['0'], $b['1'], $b['2']), VanillaBlocks::AIR());
					}
				}

			}
		}

	}


	public function PlayerJoin(PlayerJoinEvent $event) {
		$this->plugin->bridge[$event->getPlayer()->getName()] = false;
	}
}