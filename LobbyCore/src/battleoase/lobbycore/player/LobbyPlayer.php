<?php


namespace battleoase\lobbycore\player;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\BattlePlayer;
use battleoase\lobbycore\forms\SettingsForm;
use BattleOase\LobbyCore\LobbyCore;
use battleoase\lobbycore\task\DisplayTitleTask;
use battleoase\lobbycore\utils\SettingUtils;
use ceepkev77\BattleCore\provider\AsyncExecutor;
use ceepkev77\BattleCore\provider\CoinProvider;
use ceepkev77\BattleCore\provider\MYSQLProvider;
use ceepkev77\cloudapi\api\ServerAPI;
use ceepkev77\cloudapi\api\StatusAPI;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\sound\EndermanTeleportSound;
use pocketmine\world\sound\FireExtinguishSound;
use pocketmine\world\sound\FizzSound;

class LobbyPlayer
{

    /**
     * @var Player
     */
    private Player $player;
    private bool $build = false;
	public int $cooldown = 86400;

    public function __construct(Player $player)
    {
        $this->player = $player;
    }

    public function onLoad(): void {

		$playerName = $this->getPlayer()->getName();
		$lobbyPlayer = PlayerManager::getPlayer($playerName);
		$player = $lobbyPlayer->getPlayer();

		SettingUtils::register($playerName);
		$player->teleport(new Vector3(-42751, 49 + 2, -5887));
		$player->getWorld()->addSound(new Vector3($player->getPosition()->getX(), $player->getPosition()->getY(), $player->getPosition()->getZ()), new FireExtinguishSound());

		unset(LobbyCore::getInstance()->doublejump[$player->getName()]);
		unset(LobbyCore::getInstance()->protection[$player->getName()]);
		unset(LobbyCore::getInstance()->fly[$player->getName()]);

		LobbyCore::getInstance()->getJumpAndRun()->jump[$player->getName()] = false;
		unset(LobbyCore::getInstance()->getJumpAndRun()->checkpoint[$player->getName()]);

		$lobbyPlayer->setBuild(false);

		$player->getWorld()->addSound(new Vector3($player->getPosition()->getX(), $player->getPosition()->getY(), $player->getPosition()->getZ()), new FizzSound);
		$player->getWorld()->addSound(new Vector3($player->getPosition()->getX(), $player->getPosition()->getY(), $player->getPosition()->getZ()), new EndermanTeleportSound);

    	$this->giveItems();
        LobbyCore::getInstance()->getScheduler()->scheduleRepeatingTask(new DisplayTitleTask($player), 5);
	}

	/**
	 * @param Player $player
	 */
	public function setPlayer(Player $player): void
	{
		$this->player = $player;
	}

    /**
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

	public function giveItems(){
    	$player = $this->player;

    	$player->getInventory()->clearAll();

		$teleporter = ItemFactory::getInstance()->get(ItemIds::COMPASS, 0, 1);
		$teleporter->setCustomName("§8• §eTeleporter §8•");
		$teleporter->setLore(["§7Teleport to a Minigame!"]);

		$profile = ItemFactory::getInstance()->get(ItemIds::MOB_HEAD, 0, 1);
		$profile->setCustomName("§8• §3Profile §8•");
		$profile->setLore(["§7See your Statics"]);

		$lobbyswitcher = ItemFactory::getInstance()->get(ItemIds::NETHER_STAR, 0, 1);
		$lobbyswitcher->setCustomName("§8• §bLobby-Switcher §8•");
		$lobbyswitcher->setLore(["§7Switch the Lobby"]);

		$feature = ItemFactory::getInstance()->get(ItemIds::BLAZE_ROD, 0, 1);
		$feature->setCustomName("§8• §6Feature §8•");
		$feature->setLore(["§7Activate Custom-Features"]);

		$player->getInventory()->setItem(7, $feature);
		$player->getInventory()->setItem(7, $feature);
		$player->getInventory()->setItem(5, $lobbyswitcher);
		$player->getInventory()->setItem(1, $profile);
		$player->getInventory()->setItem(3, $teleporter);
	}


	public function disableBuild(){
    	$this->setBuild(false);
		$this->getPlayer()->sendMessage(LobbyCore::PREFIX . BattleCore::getInstance()->getLanguageSystem()->translate($this->getPlayer(), "lobbycore.message.buildmode.deactivate", []));
	}

	public function enableBuild(){
    	$this->setBuild(true);
		$this->getPlayer()->sendMessage(LobbyCore::PREFIX . BattleCore::getInstance()->getLanguageSystem()->translate($this->getPlayer(), "lobbycore.message.buildmode.activate", []));
	}

	/**
	 * @return bool
	 */
	public function isBuild(): bool
	{
		return $this->build;
	}

	/**
	 * @param bool $build
	 */
	public function setBuild(bool $build): void
	{
		$this->build = $build;
	}
}