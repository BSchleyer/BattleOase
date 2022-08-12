<?php

namespace battleoase\battlecore\partySystem\utils;

use battleoase\battlecore\BattleCore;
use Exception;
use pocketmine\player\Player;

class Database{

	/** @var Medoo|null */
	private $medoo;

    /**
     * Database constructor.
     * @throws Exception
     */
	public function __construct(){

	    $this->medoo = null;

        if (!$this->medoo instanceof Medoo) {
            $this->medoo = new Medoo([
                "database_type" => "mysql",
                "database_name" => "Core",
                "server" => "5.181.151.62",
                "port" => 3306,
                "username" => "admin",
                "password" => "jXStW36nxuVxFALrwybgdhNRxkKfHb9M5uzSAxgBqRPyLh3Q8Kj6e6aGwTCp6kuc",
            ]);
        }
        $this->initializeUserTable();
        $this->initializePartyTable();
	}

    /**
     * Function initializePartyTable
     * @return bool
     * @throws Exception
     */
    public function initializePartyTable(): bool{
		BattleCore::getInstance()->getLogger()->info("§bTrying to create §9parties §btable.");
        $query = $this->getMedoo()->query("CREATE TABLE IF NOT EXISTS parties(
	`party_name` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    `party_owner` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`members` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '[]',
    `isPublic` BOOLEAN NOT NULL DEFAULT false,

	UNIQUE KEY `party_name` (`party_name`) USING BTREE,
	PRIMARY KEY (`party_name`)
)ENGINE=InnoDB;")->errorCode();
        if ($query == "00000") {
            BattleCore::getInstance()->getLogger()->info("§bCreated table §9parties§b.");
            return true;
        }
		BattleCore::getInstance()->getLogger()->info("§4Failed to create §eparties §4table. Error code " . $query);
        return false;
    }

    /**
     * Function initializeGroupTable
     * @return bool
     * @throws Exception
     */
    public function initializeUserTable(): bool{
		BattleCore::getInstance()->getLogger()->info("§bTrying to create §9party_users §btable.");
        $query = $this->getMedoo()->query("CREATE TABLE IF NOT EXISTS party_users(
	`name` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    `party_name` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    `isInParty` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    `acceptRequests` BOOLEAN NOT NULL DEFAULT true,
	`requests` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '[]',

	UNIQUE KEY `name` (`name`) USING BTREE,
	PRIMARY KEY (`name`)
)ENGINE=InnoDB;")->errorCode();
        if ($query == "00000") {
			BattleCore::getInstance()->getLogger()->info("§bCreated table §9party_users§b.");
            return true;
        }
		BattleCore::getInstance()->getLogger()->info("§4Failed to create §eparty_users §4table. Error code " . $query);
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
		return $this->medoo->has("party_users", ["name" => $name]);
	}

    /**
     * Function isParty
     * @param string $name
     * @return bool
     */
    public function isParty(string $name): bool{
        return $this->medoo->has("parties", ["party_name" => $name]);
    }

	/**
	 * Function addPlayer
	 * @param Player $player
	 * @return void
	 * @throws Exception
	 */
	public function addPlayer(Player $player): void{
		$name = $player->getName();
        if (!$this->isPlayer($player->getName())) $this->medoo->insert("party_users", ["name" => $name, "party_name" => null, "isInParty" => false, "acceptRequests" => true, "requests" => BattleCore::getInstance()->partySystem::getUtils()->encodeJson([]), ""]);
	}

    /**
     * Function createParty
     * @param Player $player
     * @return void
     * @throws Exception
     */
    public function createParty(Player $player): void{
        $partyName = "{$player->getName()}_Party";
        if (!$this->isParty($player->getName())) $this->medoo->insert("parties", ["party_name" => $partyName, "party_owner" => $player->getName(), "members" => BattleCore::getInstance()->partySystem::getUtils()->encodeJson([]), "isPublic" => false]);
    }

    /**
     * Function deleteParty
     * @param Player $player
     * @return void
     * @throws Exception
     */
    public function deleteParty(Player $player): void{
        $partyName = "{$player->getName()}_Party";
        if (!$this->isParty($player->getName())) $this->medoo->delete("parties", ["party_name" => $partyName]);
    }

    /**
     * Function getPlayer
     * @param string $name
     * @return array
     * @throws Exception
     */
	public function getPlayer(string $name): array
    {
        return $this->getMedoo()->get("party_users", ["name", "party_name", "isInParty", "acceptRequests", "requests"], ["name" => $name]);
    }

    /**
     * Function getPlayer
     * @param string $name
     * @return array
     * @throws Exception
     */
    public function getParty(string $name): array
    {
        return $this->getMedoo()->get("parties", ["party_name", "party_owner", "members", "isPublic"], ["party_name" => $name]);
    }

    /**
     * Function canJumpTo
     * @param string $name
     * @return bool
     * @throws Exception
     */
    public function canJumpTo(string $name): bool{
        return $this->getPlayer($name)["canJumpTo"] ?? false;
    }

    /**
     * Function getPlayers
     * @return array
     * @throws Exception
     */
	public function getPlayers(): array{
		return $this->getMedoo()->select("party_users", ["name"]) ?? [];
	}

    /**
     * Function isInParty
     * @param string $name
     * @return bool
     * @throws Exception
     */
    public function isInParty(string $name): bool{
        $party = BattleCore::getInstance()->partySystem::getUtils()->encodeJson($this->getPlayer($name)["isInParty"]);
        return $party ?? false;
    }

    /**
     * Function isPartyRequest
     * @param string $name
     * @param string $partyName
     * @return bool
     * @throws Exception
     */
    public function isPartyRequest(string $name, string $partyName): bool{
        $requests = BattleCore::getInstance()->partySystem::getUtils()->decodeJson($this->getPlayer($name)["requests"]);
        return in_array($partyName, $requests);
    }

    /**
     * Function getRequests
     * @param string $name
     * @return array
     * @throws Exception
     */
    public function getRequests(string $name): array{
        return BattleCore::getInstance()->partySystem::getUtils()->decodeJson($this->getPlayer($name)["requests"]) ?? [];
    }

    /**
     * Function addPlayerFriend
     * @param string $name
     * @param string $partyName
     * @return void
     * @throws Exception
     */
    public function joinParty(string $name, string $partyName): void{
        $members = BattleCore::getInstance()->partySystem::getUtils()->decodeJson($this->getParty($partyName)["members"]);
        $members[] = $name;
        $this->getMedoo()->update("parties", ["members" => BattleCore::getInstance()->partySystem::getUtils()->encodeJson($members)], ["party_name" => $partyName]);
    }

    /**
     * @throws Exception
     */
    public function leaveParty(string $name, string $partyName){
        $data = $this->getParty($partyName);
        $players = json_decode($data["members"]);
        unset($players[array_search($name, $players)]);
        $this->getMedoo()->update("parties", ["members" => BattleCore::getInstance()->partySystem::getUtils()->encodeJson($players)], ["party_name" => $name]);
    }

    /**
     * Function addFriendRequest
     * @param string $name
     * @param string $partyName
     * @return void
     * @throws Exception
     */
    public function addPartyRequest(string $name, string $partyName): void{
        $requests = BattleCore::getInstance()->partySystem::getUtils()->decodeJson($this->getPlayer($name)["requests"]);
        $requests[] = $partyName;
        $this->getMedoo()->update("party_users", ["requests" => BattleCore::getInstance()->partySystem::getUtils()->encodeJson($requests)], ["name" => $name]);
    }

    /**
     * @throws Exception
     */
    public function removePartyRequest(string $name, string $friend){
        $data = $this->getPlayer($name);
        $players = json_decode($data["requests"]);
        unset($players[array_search($friend, $players)]);
        $this->getMedoo()->update("party_users", ["requests" => BattleCore::getInstance()->partySystem::getUtils()->encodeJson($players)], ["name" => $name]);
    }
}