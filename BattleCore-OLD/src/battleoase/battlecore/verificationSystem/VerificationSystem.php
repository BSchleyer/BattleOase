<?php


namespace battleoase\battlecore\verificationSystem;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\friendSystem\commands\FriendsCommand;
use battleoase\battlecore\utils\BPlugin;
use battleoase\battlecore\verificationSystem\commands\VerifyCommand;
use pocketmine\Server;

class VerificationSystem extends BPlugin
{
	const PREFIX = "§aVerification §r§f";

	public function __construct()
	{
		Server::getInstance()->getCommandMap()->register("verify", new VerifyCommand());
	}

	public function generateVerificationKey(string $playername){
		$discordName = "NULL";
		$discordId = "NULL";

		$newkey = BattleCore::getInstance()->generateRandomString(6);
		BattleCore::$connection->query("INSERT INTO Core.verify_players(`player_name`, `verify_key`, `verificationStatus`, `discordName`, `discordId`) VALUES ('$playername', '$newkey', 'isFalse', '$discordName', $discordId)");

	}

	public function getVerificationStatus(String $playername): string{
		$result = BattleCore::$connection->query("SELECT * FROM Core.verify_players WHERE player_name='$playername'");
		if ($result->num_rows > 0){
			while ($row = $result->fetch_assoc()){
				if ($row["verificationStatus"] == "isTrue"){
					return true;
				}else{
					return false;
				}
			}
		}
		return false;
	}

	public function getVerificationData(String $playername, ?string $info = null): mixed{
		$result = BattleCore::getInstance()->getMysqlConnection()->query("SELECT * FROM Core.verify_players WHERE player_name='$playername'");
		$json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
		if ($info == null) { return $json; }else{
			foreach ($json as $key => $data){
				return $data[$info];
			}
		}
		return null;
	}

}