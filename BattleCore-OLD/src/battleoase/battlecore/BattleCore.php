<?php


namespace battleoase\battlecore;

use battleoase\battlecore\chatLogSystem\ChatLogSystem;
use battleoase\battlecore\clanSystem\ClanSystem;
use battleoase\battlecore\clanWarsQueueSystem\ClanWarsQueueSystem;
use battleoase\battlecore\coinSystem\CoinSystem;
use battleoase\battlecore\customInteractSystem\CustomInteractSystem;
use battleoase\battlecore\databaseAPI\Connection;
use battleoase\battlecore\emojiSystem\EmojiSystem;
use battleoase\battlecore\eventSystem\EventSystem;
use battleoase\battlecore\externalPluginLoader\ExternalPluginLoader;
use battleoase\battlecore\friendSystem\FriendSystem;
use battleoase\battlecore\gamePassSystem\GamePassSystem;
use battleoase\battlecore\groupSystem\GroupSystem;
use battleoase\battlecore\invSortSystem\InvSortSystem;
use battleoase\battlecore\joinMeSystem\JoinMeSystem;
use battleoase\battlecore\languageSystem\LanguageSystem;
use battleoase\battlecore\npcSystem\NpcSystem;
use battleoase\battlecore\partySystem\PartySystem;
use battleoase\battlecore\pluginPlayer\PluginPlayer;
use battleoase\battlecore\privateServerSystem\PrivateServerSystem;
use battleoase\battlecore\replaySystemPlayer\ReplaySystemPlayer;
use battleoase\battlecore\replaySystemRecorder\ReplaySystemRecorder;
use battleoase\battlecore\reportSystem\ReportSystem;
use battleoase\battlecore\statsSystem\StatsSystem;
use battleoase\battlecore\verificationSystem\VerificationSystem;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class BattleCore extends PluginBase {

    /** @var BattleCore $instance */
    public static BattleCore $instance;
    /** @var false|\mysqli $connection */
    public static false|\mysqli $connection;
    /** @var LanguageSystem $languageSystem */
    public LanguageSystem $languageSystem;
    /** @var PluginPlayer $pluginPlayer */
    public PluginPlayer $pluginPlayer;
    /** @var GroupSystem $groupSystem */
    public GroupSystem $groupSystem;
    /** @var ExternalPluginLoader $externalPluginLoader */
    public ExternalPluginLoader $externalPluginLoader;
    /** @var FriendSystem $friendSystem */
    public FriendSystem $friendSystem;
    /** @var PartySystem $partySystem */
    public PartySystem $partySystem;
    /** @var ClanWarsQueueSystem $clanWarsQueueSystem */
	public ClanWarsQueueSystem $clanWarsQueueSystem;
    /** @var NpcSystem $battleNpcs */
    public NpcSystem $battleNpcs;
    /** @var StatsSystem $statsSystem */
    public StatsSystem $statsSystem;
    /** @var VerificationSystem  */
    public VerificationSystem $verificationSystem;
    /** @var PrivateServerSystem  */
	public PrivateServerSystem $privateServerSystem;
	/** @var CoinSystem  */
	public CoinSystem $coinSystem;
	/** @var JoinMeSystem  */
	public JoinMeSystem $joinME;
    /** @var CustomInteractSystem $customInteractSystem */
    public CustomInteractSystem $customInteractSystem;
    /** @var EventSystem $eventSystem */
	public EventSystem $eventSystem;
	/** @var EmojiSystem $emojiSystem */
	public EmojiSystem $emojiSystem;
	/** @var ReplaySystemRecorder $replaySystemRecorder */
	public ReplaySystemRecorder $replaySystemRecorder;
	/** @var ReplaySystemPlayer  */
	public ReplaySystemPlayer $replaySystemPlayer;
	/** @var InvSortSystem $invSortSystem */
	public InvSortSystem $invSortSystem;
	/** @var ClanSystem $clanSystem */
	public ClanSystem $clanSystem;
    /** @var GamePassSystem $gamePassSystem */
    public GamePassSystem $gamePassSystem;
    /** @var ReportSystem */
    public ReportSystem $reportSystem;
	/** @var ChatLogSystem */
	public ChatLogSystem $chatLogSystem;

	public function onLoad(): void
    {
        self::$instance = $this;
        self::$connection = mysqli_connect(self::getDataConfig()->get("address"), self::getDataConfig()->get("username"), self::getDataConfig()->get("password"));
    }

    public function onEnable(): void
    {
        $this->pluginPlayer = new PluginPlayer();
        $this->languageSystem = new LanguageSystem();
        $this->groupSystem = new GroupSystem();
        $this->externalPluginLoader = new ExternalPluginLoader();
        $this->clanWarsQueueSystem = new ClanWarsQueueSystem();
        $this->battleNpcs = new NpcSystem();
        $this->statsSystem = new StatsSystem();
        $this->verificationSystem = new VerificationSystem();
        $this->privateServerSystem = new PrivateServerSystem();
        $this->coinSystem = new CoinSystem();
        $this->joinME = new JoinMeSystem();
		$this->customInteractSystem = new CustomInteractSystem();
		$this->eventSystem = new EventSystem();
		$this->emojiSystem = new EmojiSystem();
		$this->friendSystem = new FriendSystem();
		$this->invSortSystem = new InvSortSystem();
        $this->gamePassSystem = new GamePassSystem();
        $this->partySystem = new PartySystem();
		$this->replaySystemRecorder = new ReplaySystemRecorder();
		$this->clanSystem = new ClanSystem();
		$this->reportSystem = new ReportSystem();
		$this->chatLogSystem = new ChatLogSystem();
		//$this->replaySystemPlayer = new ReplaySystemPlayer();
    }

    public function onDisable(): void
	{
	/*	if (BattleCore::getInstance()->networkSystem->originalAdaptor !== null) {
			SkinAdapterSingleton::set(BattleCore::getInstance()->networkSystem->originalAdaptor);
		}*/
	}

    public static function getDataConfig(): Config
    {
        return new Config("/home/cloud/mysql.yml", Config::YAML);
    }

	/**
     * @return LanguageSystem
     */
    public function getLanguageSystem(): LanguageSystem
    {
        return $this->languageSystem;
    }

    /**
     * @param $player
     * @param string $key
     * @param array|null $params
     * @return string
     */
    public static function translate($player, string $key, ?array $params = []): string
    {
        return BattleCore::getInstance()->languageSystem->translate($player, $key, $params);
    }

    /**
     * @return BattleCore
     */
    public static function getInstance(): BattleCore
    {
        return self::$instance;
    }

    /**
     * @return false|\mysqli
     */
    public function getMysqlConnection(): bool|\mysqli
    {
        return self::$connection;
    }

    /**
     * @return Connection|null
     */
    public function getConnection(): ?Connection
    {
        return new Connection();
    }


    public static function getPrefix(): string{
        return "§3Battle§bOase §r§f§7";
    }

	public function generateRandomString(int $length): string
	{
		$characters = "1 2 3 4 5 6 7 8 9 a b c d e f g h i j k o p q r s t u v w x y z A B C D E F G H K P Q R S T U V W X Y Z";
		$characters = explode(" ", $characters);
		$randomString = "";
		for ($n = 0; $n < $length; $n++) {
			$randomString .= $characters[mt_rand(0, count($characters) - 1)];
		}
		return $randomString;
	}

}