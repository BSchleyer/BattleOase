<?php


namespace battleoase\lobbycore\events;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\npcSystem\entities\NPCEntity;
use battleoase\battlecore\pluginPlayer\player\BPlayer;
use battleoase\battlecore\pmmpExtensions\world\FloatingTextParticle;
use battleoase\lobbycore\LobbyCore;
use battleoase\lobbycore\player\LobbyPlayer;
use battleoase\lobbycore\provider\FloatingTextsProvider;
use battleoase\lobbycore\task\AnimationPlayerJoin;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\world\sound\FireExtinguishSound;
use pocketmine\world\sound\PaintingPlaceSound;
use xenialdan\apibossbar\BossBar;

class PlayerJoinListener implements Listener
{
	public function onJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();

        $event->setJoinMessage("");

        LobbyCore::getInstance()->getPlayerManager()->registerPlayer(new LobbyPlayer($event->getPlayer()));
        LobbyCore::getInstance()->getPlayerManager()->getPlayer($event->getPlayer()->getName())->onLoad();

        foreach (FloatingTextsProvider::getFloatingTextParticles() as $floatingTextParticle) {
            Server::getInstance()->getWorldManager()->getDefaultWorld()->addParticle($floatingTextParticle->getPosition(), $floatingTextParticle, [$player]);
        }
    }
}