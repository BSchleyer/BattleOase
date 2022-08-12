<?php


namespace battleoase\bedwars\task;


use battleoase\bedwars\BedWars;
use battleoase\bedwars\classes\ItemStack;
use pocketmine\block\tile\Sign;
use pocketmine\block\utils\SignText;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class StartTask extends Task
{

	/**
	 * @var BedWars
	 */
	private BedWars $plugin;

	public function onRun(): void
	{
		$this->plugin = BedWars::getInstance();
		$this->plugin->stack = [];
		Server::getInstance()->getWorldManager()->loadWorld(BedWars::getInstance()->getArena()->getName());

		foreach (Server::getInstance()->getWorldManager()->getWorldByName(BedWars::getInstance()->getArena()->getName())->getLoadedChunks() as $chunk) {
			foreach ($chunk->getTiles() as $tile) {
				if ($tile instanceof Sign) {

					if ($tile->getText()->getLine(0) === 'bronze') {
						$vector = new Vector3($tile->getPosition()->getX() +0.5, $tile->getPosition()->getY() + 2, $tile->getPosition()->getZ() +0.5);
						$this->plugin->stack[] = new ItemStack(BedWars::getInstance()->getArena()->getName(), $vector, ItemIds::BRICK);
					}

					if ($tile->getText()->getLine(0) === 'iron') {
						$vector = new Vector3($tile->getPosition()->getX() +0.5, $tile->getPosition()->getY() + 2, $tile->getPosition()->getZ() +0.5);
						$this->plugin->stack[] = new ItemStack(BedWars::getInstance()->getArena()->getName(), $vector,ItemIds::IRON_INGOT);
					}

					if ($tile->getText()->getLine(0) === 'gold') {
						if (BedWars::getInstance()->no <= BedWars::getInstance()->yes) {
							$vector = new Vector3($tile->getPosition()->getX() + 0.5, $tile->getPosition()->getY() + 2, $tile->getPosition()->getZ() + 0.5);
							$this->plugin->stack[] = new ItemStack(BedWars::getInstance()->getArena()->getName(), $vector,ItemIds::GOLD_INGOT);
						}
					}
				}
			}
		}
		//BedWars::$replay = ReplaySystemRecorder::getInstance()->replay->startReplay(Server::getInstance()->getLevelByName(BedWars::getInstance()->getArena()->getMap()), Server::getInstance()->getLevelByName(BedWars::getInstance()->getArena()->getMap())->getSafeSpawn());
		BedWars::getInstance()->getScheduler()->scheduleRepeatingTask(new Class() extends Task{

			public int $tick = 0;

			public function onRun(): void
			{
				//Server::getInstance()->getLevelByName(BedWars::getInstance()->getArena()->getMap())->dropItem($pos->add(0.5, 2, 0.5), Item::get(Item::BRICK), new Vector3(0, 0, 0));
				$this->tick++;
				foreach (BedWars::getInstance()->stack as $itemStack) {
					if ($itemStack->getItem()->isClosed()) {
						$itemStack->spawnResource();
					}
				}
				$this->dropItems();

			}

			public function dropItems(){
				foreach (BedWars::getInstance()->stack as $itemStack) {
					if ($itemStack->getResource() == ItemIds::BRICK) {
						if (!$itemStack->getItem()->isClosed()) {
							$itemStack->addCount();
						}
					}
					if ($this->tick % 30 === 0) {
						if ($itemStack->getResource() == ItemIds::IRON_INGOT) {
							if (!$itemStack->getItem()->isClosed()) {
								$itemStack->addCount();
							}
						}
					}
					if ($this->tick % 60 === 0) {
						if ($itemStack->getResource() == ItemIds::GOLD_INGOT) {
							if (!$itemStack->getItem()->isClosed()) {
								$itemStack->addCount();
							}
						}
					}
				}
			}

		}, 20);

		/*foreach (Server::getInstance()->getOnlinePlayers() as $player) {
			$boss = new BossBar();
			$boss->setTitle("                 §l§bBed§fWars");
			$boss->setSubTitle("§eDein Team§7: " . Team::getTeamColor(BedWars::getInstance()->getTeam()->getTeamByPlayer($player)) . BedWars::getInstance()->getTeam()->getTeamByPlayer($player) . "§r§8 | §bReplay-ID:§e " . BedWars::$replay);
			$boss->setPercentage(1);
			$boss->addPlayer($player);*/

	}


}