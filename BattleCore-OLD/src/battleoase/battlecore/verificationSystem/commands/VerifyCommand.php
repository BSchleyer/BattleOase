<?php


namespace battleoase\battlecore\verificationSystem\commands;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\BattlePlayer;
use battleoase\battlecore\verificationSystem\utils\VerificationUtils;
use battleoase\battlecore\verificationSystem\VerificationSystem;;

use ceepkev77\cloudbridge\network\DataPacket;
use ceepkev77\cloudbridge\network\packet\ListCloudPlayersRequestPacket;
use ceepkev77\cloudbridge\network\packet\ListCloudPlayersResponsePacket;
use ceepkev77\cloudbridge\network\packet\PlayerMessagePacket;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class VerifyCommand extends Command
{

	public function __construct()
	{
		parent::__construct("verify", "Verification Command", "/verify", ["verification"]);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if ($sender instanceof Player) {
                $verify_key = BattleCore::getInstance()->verificationSystem->getVerificationData($sender->getName(), "verify_key");
                $status = BattleCore::getInstance()->verificationSystem->getVerificationStatus($sender->getName());
                if ($status == false) VerificationUtils::sendVerificationUi($sender, $verify_key, false); else VerificationUtils::sendVerificationUi($sender, $verify_key, true);
            }
	}
}