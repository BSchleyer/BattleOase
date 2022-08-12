<?php

declare(strict_types=1);

namespace battleoase\battlecore;

use battleoase\battlecore\clanSystem\api\PlayerClanAPI;
use battleoase\battlecore\groupSystem\GroupSystem;
use battleoase\battlecore\languageSystem\LanguageSystem;
use battleoase\battlecore\languageSystem\objects\Translation;
use battleoase\battlecore\network\customRaknet\WDPENetworkSession;
use battleoase\battlecore\player\object\Bossbar;
use battleoase\battlecore\player\object\InitializationData;
use battleoase\battlecore\player\object\Scoreboard;
use battleoase\battlecore\player\provider\PlayersProvider;
use battleoase\battlecore\player\trait\PlayerAfkTrait;
use battleoase\battlecore\player\trait\PlayerCooldownTrait;
use battleoase\battlecore\player\trait\PlayerOnlineTimeTrait;
use battleoase\battlecore\utils\AsyncExecutor;
use battleoase\battlecore\utils\Settings;
use ceepkev77\cloudbridge\network\DataPacket;
use ceepkev77\cloudbridge\network\packet\ListServerRequestPacket;
use ceepkev77\cloudbridge\network\packet\ListServerResponsePacket;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\lang\KnownTranslationKeys;
use pocketmine\lang\Language;
use pocketmine\lang\Translatable;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\types\BossBarColor;
use pocketmine\player\Player;
use pocketmine\player\PlayerInfo;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class BattlePlayer extends Player {
    use PlayerAfkTrait;
    use PlayerCooldownTrait;
    use PlayerOnlineTimeTrait;

    public const AFK_TICKS = 60000;

    public Scoreboard $scoreboard;
    private Bossbar $bossbar;
    private int $coins;

    public function __construct(Server $server, NetworkSession $session, PlayerInfo $playerInfo, bool $authenticated, Location $spawnLocation, ?CompoundTag $namedtag)
    {
        parent::__construct($server, $session, $playerInfo, $authenticated, $spawnLocation, $namedtag);
        $this->scoreboard = new Scoreboard($this);
        $this->bossbar = new Bossbar($this);
    }

    public function initialize(): void {
        $playerName = $this->getName();
        AsyncExecutor::submitMYSQLAsyncTask("Core", function (\mysqli $mysqli) use ($playerName) {
            $query = $mysqli->query("SELECT * FROM `players` WHERE player_name='$playerName'");
            if($query->num_rows > 0) {
                $coins = $query->fetch_assoc()["coins"];
            } else {
                $coins = 1000;
                //Todo: Init first join in players table
               // $mysqli->query("");
            }
            return intval($coins);
        }, function (Server $server, $result) use ($playerName): void {
            /** @var BattlePlayer|null $player */
            $player = Server::getInstance()->getPlayerExact($playerName);
            if($player === null) return;
            $player->setCoins($result, false);
        });
    }


    public function getDiscordName()
    {
        return BattleCore::getInstance()->verificationSystem->getVerificationData($this->getName(), "discordName");
    }

    /**
     * @param string|null $info
     * @return mixed
     */
    public function getNetworkLanguage(): mixed {
		$name = $this->getName();
		$result = BattleCore::getInstance()->getMysqlConnection()->query("SELECT lang FROM Core.players WHERE player_name='$name'");
		if ($result->num_rows > 0) return $result->fetch_assoc()["lang"];
		return Settings::DEFAULT_LANGUAGE;
    }


	public function isInClan(): bool {
		if (PlayerClanAPI::isInClan($this->getName())){
			return true;
		}else{
			return false;
		}

	}

    /**
     * @param string $info
     * @param $set
     */
    public function setInfo(string $info, $set)
    {
        $name = $this->getName();
        BattleCore::getInstance()->getMysqlConnection()->query("UPDATE Core.players SET $info='$set' WHERE player_name='$name'");
    }


    public function sendMessage(Translatable|Translation|string $message): void
    {
        if($message instanceof Translation) {
            $message = $this->translate($message->key, $message->parameters);
        }
        parent::sendMessage($message);
    }

	public function translate(string $key, array $parameters = []) {
		return LanguageSystem::translateFor($this, $key, $parameters);
	}

    public function kick(string $reason = "", Translatable|string|null $quitMessage = null) : bool{
        if($reason == "FALLBACK") {
            $pk = new ListServerRequestPacket();
            $pk->submitRequest($pk, function (DataPacket $dataPacket) {
                if($dataPacket instanceof ListServerResponsePacket) {
                    $servers = json_decode($dataPacket->data["servers"], true);
                    $lobbies = [];
                    foreach ($servers as $server) {
                        $text = explode("-", $server);
                        if($text[0] === "Lobby") {
                            $lobbies[] = $server;
                        }
                    }
					$lobby = $lobbies[mt_rand(0, (count($lobbies) - 1))];
					$this->transfer($lobby);
                }
            });
        }

		BattleCore::getInstance()->getScheduler()->scheduleDelayedTask(new class($this, $reason, $quitMessage) extends Task {
			protected Player $player;
			protected string $reason;
			protected string|null|Translatable $quitmessage;

			public function __construct(Player $player, string $kickreason, Translatable|string|null $quitMessage){
				$this->player = $player;
				$this->reason = $kickreason;
				$this->quitmessage = $quitMessage;
			}

			public function onRun(): void
			{
				if (!is_null($this->player) && $this->player->isConnected()){
					$ev = new PlayerKickEvent($this->player, $this->reason, $this->quitmessage ?? $this->player->getLeaveMessage());
					$ev->call();
					if(!$ev->isCancelled()){
						$reason = $ev->getReason();
						if($reason === ""){
							$reason = KnownTranslationKeys::DISCONNECTIONSCREEN_NOREASON;
						}
						$this->player->disconnect($reason, $ev->getQuitMessage());
					}
				}
			}
		}, 40);

        return false;
    }

    public function playSound(string $soundName, ?float $volume = 1.0, ?float $pitch = 1.0): void
    {
        $pk = new PlaySoundPacket();

		if (is_null($this->getNetworkSession()) or is_null($this->getLocation())) return;

        $pk->soundName = $soundName;
        $pk->volume = $volume;
        $pk->pitch = $pitch;
        $pk->x = !is_null($this->getLocation()) ? $this->getLocation()->getX() : 0;
        $pk->y = !is_null($this->getLocation()) ? $this->getLocation()->getY() : 0;
        $pk->z = !is_null($this->getLocation()) ? $this->getLocation()->getZ() : 0;

        $this->getNetworkSession()->sendDataPacket($pk);
    }

    protected function entityBaseTick(int $tickDiff = 1): bool
    {
        $this->afkTicks++;
		//if($this->isAfk())
            //Todo: Add PlayerKickPacket

        $this->bossbar->hide();
        return parent::entityBaseTick($tickDiff);
    }

    public function broadcastMovement(bool $teleport = false): void{
        parent::broadcastMovement($teleport);
        $this->resetAfkTicks();
    }

    //CoinSystem

    /**
     * @param int $coins
     */
    public function setCoins(int $coins, bool $update = true): void
    {
        $this->coins = $coins;
        if($update) {
            $playerName = $this->getName();
            AsyncExecutor::submitMYSQLAsyncTask("Core", function (\mysqli $mysqli) use ($playerName, $coins) {
                $mysqli->query("UPDATE players SET coins='$coins' WHERE player_name='$playerName'");
            });
        }

    }

    /**
     * @param int $amount
     */
    public function removeCoins(int $amount): void {
        $this->setCoins($this->getCoins() - $amount);
        $this->sendTitle(" ", "§r" . str_repeat("\n", 6) . str_repeat(" ", 36) . "§r§c-§e" . $amount . " §7Coins", 1, 18, 1);
    }



    /**
     * @param int $amount
     */
    public function addCoins(int $amount): void
    {
        $this->setCoins($this->getCoins() + $amount);
        $this->sendTitle(" ", "§r" . str_repeat("\n", 6) . str_repeat(" ", 36) . "§r§7+§e" . $amount . " §7Coins", 1, 18, 1);
    }

    public function getCoins(): int {
        return $this->coins;
    }

    public function getGroup(){
		return GroupSystem::getPlayerAPI()->getGroup($this);
	}

}