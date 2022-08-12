<?php


namespace battleoase\lobbycore\forms;


use pocketmine\data\bedrock\EffectIdMap;
use pocketmine\entity\effect\Effect;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\EffectManager;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;
use pocketmine\world\sound\EndermanTeleportSound;
use pocketmine\world\sound\FizzSound;

class Games
{
	public static Player $player;

	public function __construct(Player $player)
	{
		self::$player = $player;
	}

	public static function teleportToGame(Vector3 $vector3, String $game) {

		$player = self::$player;

		$effect = EffectIdMap::getInstance()->fromId(15);
		$tation = VanillaEffects::LEVITATION();
		$blind = VanillaEffects::BLINDNESS();
		$invisi = VanillaEffects::INVISIBILITY();

		$levi = new EffectInstance($tation, 30, 6, false);
		$ness = new EffectInstance($blind, 30, 6, false);
		$bility = new EffectInstance($invisi, 30, 6, false);
		$ei = new EffectInstance($effect, 50, 9, false);

		$player->getEffects()->add($ei);
		$player->getEffects()->add($levi);
		$player->getEffects()->add($ness);
		$player->getEffects()->add($bility);

		//$player->sendTitle("§rSpawn", "§3Battle§bOase");
		$player->sendTitle($game, "§3Battle§bOase");

		//$player->teleport(new Vector3(-42, 51.5, -58));
		$player->teleport($vector3);

		$player->getWorld()->addSound(new Vector3($player->getPosition()->getX(), $player->getPosition()->getY(), $player->getPosition()->getZ()), new FizzSound);
		$player->getWorld()->addSound(new Vector3($player->getPosition()->getX(), $player->getPosition()->getY(), $player->getPosition()->getZ()), new EndermanTeleportSound);
	}
}