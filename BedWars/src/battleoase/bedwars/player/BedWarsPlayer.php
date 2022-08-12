<?php


namespace battleoase\bedwars\player;


use battleoase\battlecore\BattleCore;
use battleoase\bedwars\api\TeamAPI;
use battleoase\bedwars\BedWars;
use battleoase\bedwars\classes\Team;
use battleoase\bedwars\shop\types\ShopMenu;
use battleoase\bedwars\utils\PlayerScoreboard;
use ceepkev77\cloudbridge\CloudBridge;
use formBridge\form\MenuForm;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\world\Position;

class BedWarsPlayer
{

    /**
     * @var Player
     */
    private Player $player;
    private ?Team $team;
	private int $kill;
	private bool $buildMode = false;

	public bool $mapVoted;
	public bool $goldVoted = false;

	private ShopMenu $shopMenu;

	public function __construct(Player $player)
    {
        $this->player = $player;
        $this->kill = 0;
        $this->mapVoted = false;
        $this->goldVoted = false;
    }

    public function onLoad()
    {
		$this->getPlayer()->teleport(Server::getInstance()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
		$this->getPlayer()->getInventory()->clearAll();
		$this->getPlayer()->getInventory()->setContents(BedWars::getInstance()->loadContents(BedWars::LOBBY_ITEMS));
		$this->getPlayer()->getXpManager()->setXpLevel(0);
		$this->getPlayer()->getHungerManager()->setFood(20);
		$this->getPlayer()->setHealth(20);
		$this->getPlayer()->setFlying(false);
		$this->getPlayer()->setAllowFlight(false);
		$this->setShopMenu(new ShopMenu());
		$this->buildMode = false;
    }

	/**
	 * @param bool $goldVoted
	 */
	public function setGoldVoted(bool $goldVoted): void
	{
		$this->goldVoted = $goldVoted;
	}

	/**
	 * @return bool
	 */
	public function hasGoldVoted(): bool
	{
		return $this->goldVoted;
	}

	/**
	 * @return ShopMenu
	 */
	public function getShopMenu(): ShopMenu
	{
		return $this->shopMenu;
	}

	/**
	 * @param ShopMenu $shopMenu
	 */
	public function setShopMenu(ShopMenu $shopMenu): void
	{
		$this->shopMenu = $shopMenu;
	}



	/**
     * @param Player $player
     */
    public function setPlayer(Player $player): void
    {
        $this->player = $player;
    }

	/**
	 * @param bool $buildMode
	 */
	public function setBuildMode(bool $buildMode): void
	{
		$this->buildMode = $buildMode;
	}

	/**
	 * @return bool
	 */
	public function getBuildMode(): bool
	{
		return $this->buildMode;
	}

    /**
     * @param Team|null $team
     */
    public function setTeam(?Team $team): void
    {
        $this->team = $team;
        if($team instanceof Team) {
			$team->addPlayer($this->getPlayer());
			$this->getPlayer()->sendMessage(BedWars::PREFIX. BattleCore::translate($this->getPlayer(), "bedwars.message.nowInTeam", [
					"{TEAM}" => $team->getDisplayName()
				]));


			$scoreboard = new PlayerScoreboard();
			$scoreboard->scoreboard($this->getPlayer());
          //  $this->getPlayer()->setNameTag(TeamAPI::getTeamColor($team->getName()) . $team->getName() . "§8 | " . TeamAPI::getTeamColor($team->getName()) . $team->getName());
          //  $this->getPlayer()->setDisplayName(TeamAPI::getTeamColor($team->getName()) . $team->getName() . "§8 | " . TeamAPI::getTeamColor($team->getName()) . $team->getName());

        }
    }

    public function removeTeam(): void
    {
        if($this->team instanceof Team) {
            $this->team->removePlayer($this->getPlayer());
        }
    }

    /**
     * @return Team|null
     */
    public function getTeam(): ?Team
    {
        return $this->team;
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

	public function teleport()
	{
		$map = BedWars::getInstance()->getArena()->getName();
		$team = $this->getTeam()->getName();
		$config = new Config("/home/cloud/templates/" . CloudBridge::getInstance()->getTemplate() . "/plugin_data/BedWars/arena/{$map}.yml", Config::YAML);
		$x = $config->getNested("Spawn.{$team}.x");
		$y = $config->getNested("Spawn.{$team}.y");
		$z = $config->getNested("Spawn.{$team}.z");
		Server::getInstance()->getWorldManager()->loadWorld($map);
		$this->player->teleport(new Position($x, $y, $z, Server::getInstance()->getWorldManager()->getWorldByName($map)));

		$this->player->getInventory()->clearAll();
		$this->player->getArmorInventory()->clearAll();
		$this->player->getXpManager()->setXpLevel(0);
		$this->player->getHungerManager()->setFood(20);
		$this->player->setHealth(20);
    }

	/**
	 * @return int
	 */
	public function getKill(): int
	{
		return $this->kill;
	}

	public function addKill(): void
	{
		$this->kill++;
	}

	/**
	 * @param bool $voted
	 */
	public function setMapVote(bool $voted): void
	{
		$this->mapVoted = $voted;
	}

	/**
	 * @return bool
	 */
	public function hasMapVoted(): bool
	{
		return $this->mapVoted;
	}
}