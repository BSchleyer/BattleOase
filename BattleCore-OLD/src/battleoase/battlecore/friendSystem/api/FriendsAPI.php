<?php

namespace battleoase\battlecore\friendSystem\api;

use battleoase\battlecore\BattleCore;
use battleoase\battlecore\BattlePlayer;
use battleoase\battlecore\friendSystem\database\Database;
use battleoase\battlecore\friendSystem\FriendSystem;
use ceepkev77\cloudbridge\network\packet\PlayerMessagePacket;
use Frago9876543210\EasyForms\elements\Button;
use Frago9876543210\EasyForms\elements\Image;
use Frago9876543210\EasyForms\elements\Input;
use Frago9876543210\EasyForms\elements\Toggle;
use Frago9876543210\EasyForms\forms\CustomForm;
use Frago9876543210\EasyForms\forms\CustomFormResponse;
use Frago9876543210\EasyForms\forms\MenuForm;
use pocketmine\crafting\FurnaceRecipe;
use pocketmine\network\mcpe\protocol\EmoteListPacket;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class FriendsAPI {

    /**
     * Function encodeJson
     * @param array $array
     * @return string
     */
    public function encodeJson(array $array): string{
        return json_encode($array);
    }

    /**
     * Function decodeJson
     * @param string $string
     * @return array
     */
    public function decodeJson(string $string): array{
        return json_decode($string);
    }

    /**
     * Function sendFriendsUI
     * @param Player $player
     * @return void
     */
    public static function sendFriendsUI(Player $player): void
    {
		$name = $player->getName();
		$requests = count((new Database())->getFriendRequests($name));

        $buttons = [BattleCore::translate($player, "FriendSystem.ui.button.listFriends"), BattleCore::translate($player, "FriendSystem.ui.button.friendRequests", ["{COUNT}" => $requests]), BattleCore::translate($player, "FriendSystem.ui.button.addFriend"), BattleCore::translate($player, "FriendSystem.ui.button.settings")];
        $player->sendForm(new MenuForm(
            "§8• §aFriends §8•",
            "",
            $buttons,
            function (Player $player, Button $button): void {
                switch ((int)$button->getValue()) {
                    case 0:
                        self::sendFriendList($player);
                        break;
                    case 1:
                        self::sendFriendRequests($player);
                        breaK;
                    case 2:
                        self::sendFriendRequest($player);
                        break;
                    case 3:
                        self::sendYourFriendSettings($player);
                        break;
                }
            }
        ));
    }

    public static function sendYourFriendSettings(Player $player)
    {
        $player->sendForm(new CustomForm("§8• §aFriends §8•",
            [
                new Toggle("jump", ((new Database())->canJumpTo($player->getName())))
            ], function (Player $player, CustomFormResponse $response) : void {
                list($jump) = $response->getValues();
                $name = $player->getName();
                if($jump) {
                    BattleCore::getInstance()->getMysqlConnection()->query("UPDATE Core.`friends` SET `canJumpTo`=true WHERE `name`='$name'");
                } else {
                    BattleCore::getInstance()->getMysqlConnection()->query("UPDATE Core.`friends` SET `canJumpTo`=false WHERE `name`='$name'");
                }

            }));
    }

    /**
     * Function sendFriendRequest
     * @param Player $player
     * @return void
     */
    public static function sendFriendRequest(Player $player): void{
        $player->sendForm(new CustomForm(
            "§8• §aFriends §8•",
            [
                new Input("", "xXZLoniuxX2"),
            ],
            function (Player $player, CustomFormResponse $response): void {
                $input = $response->getInput();
                if ((new Database())->isPlayer($input->getValue())){
                    if (!(new Database())->isPlayerFriend($player->getName(), $input->getValue())){
                        if (!(new Database())->isFriendRequest($input->getValue(), $player->getName())){
                            (new Database())->addFriendRequest($input->getValue(), $player->getName());
                            if (strtolower(TextFormat::clean($input->getValue())) !== strtolower($player->getName())) {
                                $player->sendMessage(FriendSystem::PREFIX . BattleCore::getInstance()->languageSystem->translate($player, "FriendSystem.send.friendRequest", [
										"{TO}" => $input->getValue()
									]));
                                $playerInput = TextFormat::clean($input->getValue());
                                $pk = new PlayerMessagePacket();
                                $pk->playerName = $playerInput;
                                $pk->value = FriendSystem::PREFIX . BattleCore::getInstance()->languageSystem->translate($playerInput, "FriendSystem.received.friendRequest", [
									"{BY}" => $player->getName()
									]);
                                $pk->sendPacket();

                            } else {
                                $player->sendMessage(FriendSystem::PREFIX . BattleCore::getInstance()->languageSystem->translate($player, "FriendSystem.error"));
                            }
                        } else {
                            $player->sendMessage(FriendSystem::PREFIX . BattleCore::getInstance()->languageSystem->translate($player, "FriendSystem.already.send"));
                        }
                    } else {
                        $player->sendMessage(FriendSystem::PREFIX . BattleCore::getInstance()->languageSystem->translate($player, "FriendSystem.already.yourFriend"));
                    }
                } else {
                    $player->sendMessage(FriendSystem::PREFIX . BattleCore::getInstance()->languageSystem->translate($player, "FriendSystem.player.notExist"));
                }
            }
        ));
    }

    /**
     * Function sendFriendList
     * @param Player $player
     * @return void
     */
    public static function sendFriendList(Player $player): void
    {
        $buttons = [];
        foreach ((new Database())->getPlayerFriends($player->getName()) as $friend) {
            if (!is_null($friend)) {
                $buttons[] = new Button($friend, new Image("http://battleoase.net/api/battleoase/players/$friend/head/$friend.png"));
                asort($buttons);
            }else{
            	$buttons[] = new Button("§cNONE", new Image("https://icons.iconarchive.com/icons/google/noto-emoji-symbols/128/73030-no-entry-icon.png"));
			}
        }
        $player->sendForm(new MenuForm(
            "§8• §eFriend List §8•",
            "",
			$buttons,
            function (Player $player, Button $button): void {
                $str = explode("\n", $button->getText())[0];
                if ((new Database())->isPlayerFriend($player->getName(), $str)){
                    self::sendFriendSettings($player, $str);
                }
            }
        ));
    }

	/**
	 * Function sendFriendRequests
	 * @param Player $player
	 * @return void
	 */
	public static function sendFriendRequests(Player $player): void
	{
		$buttons = ["§aAccept all", "§cDeny all"];
		foreach ((new Database())->getFriendRequests($player->getName()) as $request) {
			if (!is_null($request)) {
				$buttons[] = $request;
			}
		}
		$player->sendForm(new MenuForm(
			"§8• §cFriend requests §8•",
			"",
			$buttons,
			function (Player $player, Button $button): void {
				$str = explode("\n", $button->getText())[0];
				if (TextFormat::clean($str) === "Accept all") {
					if (count((new Database())->getPlayerFriends($player->getName()))+count((new Database())->getFriendRequests($player->getName())) < 100) {
						foreach ((new Database())->getFriendRequests($player->getName()) as $request) {
							if (!is_null($request)) {
								$pk = new PlayerMessagePacket();
								$pk->playerName = $request;
								$pk->value = FriendSystem::PREFIX . "§e{$player->getName()} §ahas accepted your friend request§8.";
								$pk->sendPacket();

								(new Database())->removeFriendRequest($player->getName(), $request);
								(new Database())->removeFriendRequest($request, $player->getName());
								(new Database())->addPlayerFriend($player->getName(), $request);
								(new Database())->addPlayerFriend($request, $player->getName());
							}
						}
						$player->sendMessage(FriendSystem::PREFIX . "§aYou have accepted all §eFriend requests§8.");
					} else {
						$player->sendMessage(FriendSystem::PREFIX . "§cYou cannot accept all friend requests, otherwise you would exceed the maximum number of friends§8.");
					}
					return;
				}
				if (TextFormat::clean($str) === "Deny all") {
					foreach ((new Database())->getFriendRequests($player->getName()) as $request) {
						if (!is_null($request)) {
							$pk = new PlayerMessagePacket();
							$pk->playerName = $request;
							$pk->value = FriendSystem::PREFIX . "§e{$player->getName()} §chas denied your friend request§8.";
							$pk->sendPacket();

							(new Database())->removeFriendRequest($player->getName(), $request);
						}
					}
					$player->sendMessage(FriendSystem::PREFIX . "§cYou have denied all §eFriend requests§8.");
					return;
				}
				if ((new Database())->isFriendRequest($player->getName(), TextFormat::clean($str))) {
					self::acceptFriendUI($player, TextFormat::clean($str));
				}
			}
		));
	}

	/**
	 * Function acceptFriendUI
	 * @param Player $player
	 * @param string $name
	 * @return void
	 */
	public static function acceptFriendUI(Player $player, string $name): void
	{
		$buttons = ["§aAccept", "§cDeny"];

		$player->sendForm(new MenuForm(
			"§8• §eFriend Infos §8•",
			"",
			$buttons,
			function (Player $player, Button $button) use ($name) : void {
				$str = explode("\n", $button->getText())[0];
				if (TextFormat::clean($str) === "Accept") {
					if (count((new Database())->getPlayerFriends($player->getName())) < 100) {
						$friend = TextFormat::clean($name);
						(new Database())->removeFriendRequest($player->getName(), $friend);
						(new Database())->removeFriendRequest($friend, $player->getName());
						(new Database())->addPlayerFriend($player->getName(), $friend);
						(new Database())->addPlayerFriend($friend, $player->getName());
						$player->sendMessage(FriendSystem::PREFIX . "§aYou have accepted the §eFriend request §aby §e{$friend}§8.");
						$pk = new PlayerMessagePacket();
						$pk->playerName = $friend;
						$pk->value = FriendSystem::PREFIX . "§e{$player->getName()} §ahas accepted your friend request§8.";
						$pk->sendPacket();
					} else {
						$player->sendMessage(FriendSystem::PREFIX . "§cYou cannot accept any more friend requests because you have already reached the maximum number of friends§8.");
					}
				} elseif (TextFormat::clean($str) === "Deny"){
					$friend = TextFormat::clean($name);
					(new Database())->removeFriendRequest($player->getName(), $friend);
					(new Database())->removeFriendRequest($friend, $player->getName());
					$player->sendMessage(FriendSystem::PREFIX . "§aYou have accepted the §eFriend request §aby §e{$friend}§8.");
					$pk = new PlayerMessagePacket();
					$pk->playerName = $friend;
					$pk->value = FriendSystem::PREFIX . "§e{$player->getName()} §chas denied your friend request§8.";
					$pk->sendPacket();
				}
			}
		));
	}


	/**
     * Function sendFriendSettings
     * @param Player $player
     * @param string $name
     * @return void
     */
    public static function sendFriendSettings(Player $player, string $name): void
    {
        $buttons = ["§aJump to", "§cRemove Friend"];
        $player->sendForm(new MenuForm(
            "§8• §eFriend Settings §8•",
            "",
            $buttons,
            function (Player $player, Button $button) use ($name) : void {
                $str = explode("\n", $button->getText())[0];
                if (TextFormat::clean($str) === "Jump to") {
                    if (!(new Database())->canJumpTo($name)) {
                        $player->sendMessage(FriendSystem::PREFIX . "§cYou can't jump to this player.");
                    }
                }
                if (TextFormat::clean($str) === "Remove Friend") {
                    if ((new Database())->isPlayerFriend($player->getName(), $name)) {
                        (new Database())->removePlayerFriend($player->getName(), $name);
                        (new Database())->removePlayerFriend($name, $player->getName());
                        $player->sendMessage(FriendSystem::PREFIX . "§cYou have removed §e{$name} §cfrom your Friendlist§8.");
                    }
                }
            }
        ));
    }


}