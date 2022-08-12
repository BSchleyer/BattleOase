<?php

namespace battleoase\battlecore\friendSystem\database;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\friendSystem\api\FriendsAPI;
use battleoase\battlecore\friendSystem\FriendSystem;
use Exception;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;

class Database{
	/** @var Medoo */
	private $medoo;


	/**
	 * Database constructor.
	 */
	public function __construct(){

	    $this->medoo = null;

        if (!$this->medoo instanceof Medoo) {
            $this->medoo = new Medoo([
                "database_type" => "mysql",
                "database_name" => "Core",
                "server" => "5.181.151.62",
                "port" => "3306",
                "username" => "admin",
                "password" => FriendSystem::getInstance()->getMysqlPassword(),
            ]);
        }
	}

    /**
     * Function initializeGroupTable
     * @return bool
     */
    public function initializeUserTable(): bool{
        $query = $this->getMedoo()->query("CREATE TABLE IF NOT EXISTS friends(
	`name` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`friends` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`canJumpTo` BOOLEAN NOT NULL DEFAULT false,
    `requests` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,

	UNIQUE KEY `name` (`name`) USING BTREE,
	PRIMARY KEY (`name`)
)ENGINE=InnoDB;")->errorCode();
        if ($query == "00000") {
            return true;
        }
        BattleCore::getInstance()->getLogger()->info("§4Failed to create §efriends §4table. Error code " . $query);
        return false;
    }

    /**
     * Function getMedoo
     * @return Medoo
     * @throws Exception
     */
    private function getMedoo(): Medoo{
        return $this->medoo;
    }

    /**
     * Function isPlayer
     * @param string $name
     * @return bool
     */
    public function isPlayer(string $name): bool{
        return $this->medoo->has("friends", ["name" => $name]);
    }

    /**
     * Function addPlayer
     * @param Player $player
     * @return void
     * @throws Exception
     */
    public function addPlayer(Player $player): void{
        $name = $player->getName();
        if (!$this->isPlayer($player->getName())) $this->medoo->insert("friends", ["name" => $name, "friends" => (new FriendsAPI())->encodeJson([]), "canJumpTo" => false, "requests" => (new FriendsAPI())->encodeJson([])]);
    }

    /**
     * Function getPlayer
     * @param string $name
     * @return array
     */
    public function getPlayer(string $name): array
    {
        return $this->getMedoo()->get("friends", ["name", "friends", "canJumpTo", "requests"], ["name" => $name]);
    }

    /**
     * Function canJumpTo
     * @param string $name
     * @return bool
     */
    public function canJumpTo(string $name): bool{
        return $this->getPlayer($name)["canJumpTo"] ?? false;
    }

    /**
     * Function getPlayers
     * @return array
     */
    public function getPlayers(): array{
        return $this->getMedoo()->select("friends", ["name"]) ?? [];
    }

    /**
     * Function isPlayerFriend
     * @param string $name
     * @param string $friend
     * @return bool
     */
    public function isPlayerFriend(string $name, string $friend): bool{
        $friends = (new FriendsAPI())->decodeJson($this->getPlayer($name)["friends"]);
        return in_array($friend, $friends);
    }

    /**
     * Function isFriendRequest
     * @param string $name
     * @param string $friend
     * @return bool
     */
    public function isFriendRequest(string $name, string $friend): bool{
        $friends = (new FriendsAPI())->decodeJson($this->getPlayer($name)["requests"]);
        return in_array($friend, $friends);
    }

    /**
     * Function getPlayerFriends
     * @param string $name
     * @return array
     */
    public function getPlayerFriends(string $name): array{
        return (new FriendsAPI())->decodeJson($this->getPlayer($name)["friends"]) ?? [];
    }

    /**
     * Function getFriendRequests
     * @param string $name
     * @return array
     */
    public function getFriendRequests(string $name): array{
        return (new FriendsAPI())->decodeJson($this->getPlayer($name)["requests"]) ?? [];
    }

    /**
     * Function addPlayerFriend
     * @param string $name
     * @param string $friend
     * @return void
     * @throws Exception
     */
    public function addPlayerFriend(string $name, string $friend): void{
        $friends = (new FriendsAPI())->decodeJson($this->getPlayer($name)["friends"]);
        $friends[] = $friend;
        $this->getMedoo()->update("friends", ["friends" => (new FriendsAPI())->encodeJson($friends)], ["name" => $name]);
    }

    /**
     * @throws Exception
     */
    public function removePlayerFriend(string $name, string $friend){
        $data = $this->getPlayer($name);
        $players = json_decode($data["friends"]);
        unset($players[array_search($friend, $players)]);
        $this->getMedoo()->update("friends", ["friends" => (new FriendsAPI())->encodeJson($players)], ["name" => $name]);
    }

    /**
     * Function addFriendRequest
     * @param string $name
     * @param string $friend
     * @return void
     * @throws Exception
     */
    public function addFriendRequest(string $name, string $friend): void{
        $friends = (new FriendsAPI())->decodeJson($this->getPlayer($name)["requests"]);
        $friends[] = $friend;
        $this->getMedoo()->update("friends", ["requests" => (new FriendsAPI())->encodeJson($friends)], ["name" => $name]);
    }

    /**
     * @throws Exception
     */
    public function removeFriendRequest(string $name, string $friend){
        $data = $this->getPlayer($name);
        $players = json_decode($data["requests"]);
        unset($players[array_search($friend, $players)]);
        $this->getMedoo()->update("friends", ["requests" => (new FriendsAPI())->encodeJson($players)], ["name" => $name]);
    }


}