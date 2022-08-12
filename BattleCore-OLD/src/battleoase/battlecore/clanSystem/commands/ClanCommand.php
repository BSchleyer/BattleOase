<?php


namespace battleoase\battlecore\clanSystem\commands;


use battleoase\battlecore\clanSystem\api\ClanAPI;
use battleoase\battlecore\clanSystem\api\InviteAPI;
use battleoase\battlecore\clanSystem\api\PlayerClanAPI;
use battleoase\battlecore\clanSystem\ClanSystem;
use battleoase\battlecore\clanSystem\forms\DefaultClanForm;
use battleoase\battlecore\clanSystem\forms\MainClanForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat as c;

class ClanCommand extends Command
{

	public function __construct()
	{
		parent::__construct('clan', 'Clan Command!', null, ["c"]);

	}

	public function execute(CommandSender $player, string $commandLabel, array $args)
	{
		if ($player instanceof Player) {
			if (isset($args[0])) {
				switch ($args[0]) {
					case 'create':
						if (isset($args[2])) {
							$clan = $args[1];
							if (!PlayerClanAPI::isInClan($player)) {
								if (!ClanAPI::isClan($clan)) {
									if (!ClanAPI::isClanTag($args[2])) {
										ClanAPI::createClan($player, $clan, $args[2]);
										PlayerClanAPI::setPlayersClan($player, $clan, ClanSystem::LEADER);
										$player->sendMessage(ClanSystem::PREFIX.c::YELLOW."You created a new clan!");
									} else {
										$player->sendMessage(ClanSystem::PREFIX.c::YELLOW."Clantag is already in use!");
									}
								} else {
									$player->sendMessage(ClanSystem::PREFIX.c::YELLOW."Clanname is already in use!");
								}
							} else {
								$player->sendMessage(ClanSystem::PREFIX.c::YELLOW."Please leave your clan to create a new!");
							}

						}
						break;
					case 'member':
						if (($clan = PlayerClanAPI::getPlayersClanData($player)) != null) {
							$player->sendMessage(c::YELLOW.'List of clanmember');
							foreach (PlayerClanAPI::getPlayersInClan($clan->getClanName()) as $p_name) {
								$player->sendMessage(c::YELLOW."  - ".c::GRAY.$p_name);
							}
						}

						break;
					case 'leave':
						if (PlayerClanAPI::isInClan($player)) {
							if ((PlayerClanAPI::getPlayersClanData($player))->getRank() != 0) {
								PlayerClanAPI::unsetPlayersClan($player);
								$player->sendMessage(ClanSystem::PREFIX.c::YELLOW."You left the clan!");
							}else {
								$player->sendMessage(ClanSystem::PREFIX.c::YELLOW."You are the Leader of this clan! -> /clan remove");
							}
						} else {
							$player->sendMessage(ClanSystem::PREFIX.c::YELLOW."You are not in a clan");
						}
						break;
					case 'remove':
						if (PlayerClanAPI::isInClan($player)) {
							if (($data = PlayerClanAPI::getPlayersClanData($player))->getRank() == 0) {
								foreach (PlayerClanAPI::getPlayersInClan($data->getClanName()) as $p_name) {
									PlayerClanAPI::unsetPlayersClan($p_name);
								}
								ClanAPI::removeClan($data->getClanName());
								$player->sendMessage(ClanSystem::PREFIX.c::YELLOW."You deleted the clan!");
							} else {
								$player->sendMessage(ClanSystem::PREFIX.c::YELLOW."To delete the clan you musst be the Leader");
							}
						} else {
							$player->sendMessage(ClanSystem::PREFIX.c::YELLOW."You are not in a clan");
						}
						break;
					case 'join':
						if (isset($args[1]) and $player->hasPermission('clan.join')) {
							if (!PlayerClanAPI::isInClan($player)) {
								$clan = $args[1];
								if (ClanAPI::isClan($args[1])) {
									PlayerClanAPI::setPlayersClan($player, $args[1]);
									$player->sendMessage(ClanSystem::PREFIX.c::YELLOW."You joined the clan!");
								} else {
									$player->sendMessage(ClanSystem::PREFIX.c::YELLOW."$clan is not a clan!");
								}
							} else {
								$player->sendMessage(ClanSystem::PREFIX.c::YELLOW."You are already in a clan");
							}
						}
						break;
					case 'invite':
						if (isset($args[1])) {
							if (PlayerClanAPI::isInClan($player)) {
								$data = PlayerClanAPI::getPlayersClanData($player);
								if ($data->getRank() <= 1) {
									if (!InviteAPI::hasInvite($args[1], $data->getClanName())) {
										InviteAPI::invitePlayer($args[1], $data->getClanName());
										if (($p = Server::getInstance()->getPlayerExact($args[1])) instanceof Player) {
											$p->sendMessage(ClanSystem::PREFIX.c::YELLOW.'You invited to the clan '.$data->getClanName());
										}
									}
									$player->sendMessage(ClanSystem::PREFIX.c::YELLOW."You invited ".$args[1]);
								}
							} else {
								$player->sendMessage(ClanSystem::PREFIX.c::YELLOW."You are not in a clan");
							}
						}
						break;
					case 'invites':
						$player->sendMessage(c::YELLOW."List of open Invites");
						foreach (InviteAPI::getInvites($player->getName()) as $invite) {
							$player->sendMessage(c::YELLOW."  - ".c::GRAY.$invite);
						}
						break;
					case 'accept':
						if (isset($args[1])) {
							if (!PlayerClanAPI::isInClan($player)) {
								$clan = $args[1];
								if (InviteAPI::hasInvite($player->getName(), $clan)) {
									if (ClanAPI::isClan($clan)) {
										PlayerClanAPI::setPlayersClan($player, $args[1]);
										$player->sendMessage(ClanSystem::PREFIX.c::YELLOW."You joined the clan!");
									} else {
										$player->sendMessage(ClanSystem::PREFIX.c::YELLOW."The clan does not exists!");
									}

									InviteAPI::removeInvite($player->getName(), $clan);

								} else {
									$player->sendMessage(ClanSystem::PREFIX.c::YELLOW."There is no invite from this clan");
								}
							} else {
								$player->sendMessage(ClanSystem::PREFIX.c::YELLOW."You are already in a clan");
							}
						}
						break;
					case 'deny':
						if (isset($args[1])) {
							$clan = $args[1];
							if (InviteAPI::hasInvite($player->getName(), $clan)) {
								InviteAPI::removeInvite($player->getName(), $clan);
								$player->sendMessage(ClanSystem::PREFIX.c::YELLOW."You denied the clan invite!");
							} else {
								$player->sendMessage(ClanSystem::PREFIX.c::YELLOW."There is no invite from this clan");
							}
						}
						break;
					default:
						break;
				}
			} else {
				if (!PlayerClanAPI::isInClan($player)) {
					$player->sendForm(new DefaultClanForm());
				}else{
					$player->sendForm(new MainClanForm($player));
				}

				/*
				$player->sendMessage(ClanSystem::PREFIX.c::YELLOW."/clan create <clan_name> <clan_tag>");
				$player->sendMessage(ClanSystem::PREFIX.c::YELLOW."/clan member");
				$player->sendMessage(ClanSystem::PREFIX.c::YELLOW."/clan leave");
				$player->sendMessage(ClanSystem::PREFIX.c::YELLOW."/clan remove");
				$player->sendMessage(ClanSystem::PREFIX.c::YELLOW."/clan join");
				$player->sendMessage(ClanSystem::PREFIX.c::YELLOW."/clan invite <player>");*/
			}
		}

	}

}