<?php

namespace battleoase\battlecore\replaySystemRecorder;

use battleoase\battlecore\BattleCore;
use battleoase\battlecore\replaySystemRecorder\events\EntityMoveEvent;
use battleoase\battlecore\utils\CustomTask;
use pocketmine\block\Block;
use pocketmine\block\TNT;
use pocketmine\entity\Entity;
use pocketmine\entity\object\ItemEntity;
use pocketmine\entity\object\Painting;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockBurnEvent;
use pocketmine\event\block\BlockGrowEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockSpreadEvent;
use pocketmine\event\block\BlockUpdateEvent;
use pocketmine\event\block\LeavesDecayEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDespawnEvent;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerBucketEmptyEvent;
use pocketmine\event\player\PlayerBucketFillEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerGameModeChangeEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\event\server\QueryRegenerateEvent;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\network\mcpe\protocol\types\LevelEvent;
use pocketmine\network\mcpe\protocol\types\PlayerAction;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class EventListener implements Listener {

    /**
     * EventListener constructor.
     */

    public function __construct() {
        Server::getInstance()->getPluginManager()->registerEvents($this, BattleCore::getInstance());
    }

    /**
     * @priority HIGHEST
     * @param PlayerJoinEvent $event
     */

    public function onJoin(PlayerJoinEvent $event): void {
        $event->getPlayer()->save();
    }

    /**
     * @param BlockBreakEvent $event
     * @priority MONITOR
     */

    public function onBlockBreak(BlockBreakEvent $event) {
        $replay = BattleCore::getInstance()->replaySystemRecorder->getReplay();
        if($replay->isRunning() && !$event->isCancelled()){
            if($event->getPlayer()->isSpectator()) {
                return;
            }
            $replay->addAction()->breakAction($event->getBlock());
        }
    }

    /**
     * @param PlayerInteractEvent $event
     * @priority MONITOR
     */

    public function onInteract(PlayerInteractEvent $event): void {
        if($event->getAction() !== PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            return;
        }
        if($event->getItem()->getId() != ItemIds::BUCKET) {
            return;
        }
        $block = $event->getBlock();
        $replay = BattleCore::getInstance()->replaySystemRecorder->getReplay();
        if($replay->isRunning() && !$event->isCancelled()){
            if($event->getPlayer()->isSpectator()) {
                return;
            }
            $vector = $block->getPosition()->asVector3();
            $level = $block->getPosition()->getWorld();
            new CustomTask(1, function () use ($vector, $replay, $level): void {
                if(!$replay->isRunning()) return;
                $replay->addAction()->placeAction($level->getBlock($vector));
                $replay->addAction()->onBlockUpdate($level->getBlock($vector));
            });
        }
    }

    /**
     * @param PlayerBucketEmptyEvent $event
     * @priority MONITOR
     */

    public function onBucketEmpty(PlayerBucketEmptyEvent $event): void {
        $block = $event->getBlockClicked();
        $replay = BattleCore::getInstance()->replaySystemRecorder->getReplay();
        if($replay->isRunning() && !$event->isCancelled()){
            if($event->getPlayer()->isSpectator()) {
                return;
            }
            $vector = $block->getPosition()->asVector3();
            $level = $block->getPosition()->getWorld();
            new CustomTask(1, function (int $tick) use ($vector, $replay, $level): void {
                if(!$replay->isRunning()) return;
                $replay->addAction()->placeAction($level->getBlock($vector));
                $replay->addAction()->onBlockUpdate($level->getBlock($vector));
            });
        }
    }

    /**
     * @param BlockPlaceEvent $event
     * @priority MONITOR
     */

    public function onBlockPlace(BlockPlaceEvent $event) {
        $replay = BattleCore::getInstance()->replaySystemRecorder->getReplay();
        if($replay->isRunning() && !$event->isCancelled()){
            if($event->getPlayer()->isSpectator()) {
                return;
            }
            $replay->addAction()->placeAction($event->getBlock());
        }
    }

    /**
     * @param PlayerMoveEvent $event
     * @priority MONITOR
     */

    public function onMove(PlayerMoveEvent $event) {
        $replay = BattleCore::getInstance()->replaySystemRecorder->getReplay();

        if($replay->isRunning() && !$event->isCancelled()){
            if($event->getPlayer()->isSpectator()) {
                return;
            }
            $replay->addAction()->playerMoveAction($event->getPlayer());
        }
    }

    /**
     * @param PlayerAnimationEvent $event
     * @priority MONITOR


    public function onAnimate(PlayerAnimationEvent $event) {
        $replay = BattleCore::getInstance()->replaySystemRecorder->getReplay();
        if($replay->isRunning()){
            if($event->getPlayer()->isSpectator()) {
                return;
            }
            $replay->addAction()->animateAction($event->getPlayer(), $event->getAnimationType());
            $replay->addAction()->itemHeldAction($event->getPlayer());
        }
    }*/

    /**
     * @param EntityDamageEvent $event
     * @priority MONITOR
     */

    public function onDamage(EntityDamageEvent $event) {
        $player = $event->getEntity();
        if(!$player instanceof Player){
            return;
        }
        $replay = BattleCore::getInstance()->replaySystemRecorder->getReplay();
        if($replay->isRunning() && $replay->isReplayWorld($player->getWorld()) && !$event->isCancelled()){
            if($player->isSpectator()) {
                return;
            }
            $replay->addAction()->damageAction($player);
            $replay->addAction()->itemHeldAction($player);
        }
    }

    /**
     * @param PlayerToggleSneakEvent $event
     * @priority MONITOR
     */

    public function onSneak(PlayerToggleSneakEvent $event) {
        $replay = BattleCore::getInstance()->replaySystemRecorder->getReplay();
        if($replay->isRunning() && !$event->isCancelled()){
            if($event->getPlayer()->isSpectator()) {
                return;
            }
            $replay->addAction()->sneakAction($event->getPlayer(), $event->isSneaking());
        }
    }

    /**
     * @param PlayerItemConsumeEvent $event
     * @priority MONITOR
     */

    public function consumeItem(PlayerItemConsumeEvent $event) {
        $replay = BattleCore::getInstance()->replaySystemRecorder->getReplay();
        if($replay->isRunning() && !$event->isCancelled()){
            if($event->getPlayer()->isSpectator()) {
                return;
            }
            $replay->addAction()->consumeItemAction($event->getPlayer());
        }
    }

    /**
     * @param PlayerQuitEvent $event
     * @priority MONITOR
     */

    public function onQuit(PlayerQuitEvent $event) {
        $replay = BattleCore::getInstance()->replaySystemRecorder->getReplay();
        if($replay->isRunning()){
            if($event->getPlayer()->isSpectator()) {
                return;
            }
            $replay->addAction()->quitAction($event->getPlayer());
        }
    }

    /**
     * @param PlayerDeathEvent $event
     * @priority MONITOR
     */

    public function onDeath(PlayerDeathEvent $event) {
        $replay = BattleCore::getInstance()->replaySystemRecorder->getReplay();
        if($replay->isRunning()){
            if($event->getPlayer()->isSpectator()) {
                return;
            }
            $replay->addAction()->deathAction($event->getPlayer());
        }
    }

    public function onChat(PlayerChatEvent $event) {
        $replay = BattleCore::getInstance()->replaySystemRecorder->getReplay();
        if($replay->isRunning()) {
            $replay->addAction()->chatAction($event->getMessage());
        }
    }

    /**
     * @param EntityDespawnEvent $event
     * @priority MONITOR
     */

    public function onEntityDespawn(EntityDespawnEvent $event) {
        $replay = BattleCore::getInstance()->replaySystemRecorder->getReplay();
        if($replay->isRunning() && $replay->isReplayWorld($event->getEntity()->getWorld())){
            $replay->addAction()->despawnEntityAction($event->getEntity());
        }
    }

    /**
     * @param PlayerGameModeChangeEvent $event
     */

    public function onGameModeChange(PlayerGameModeChangeEvent $event): void {
        $replay = BattleCore::getInstance()->replaySystemRecorder->getReplay();
        $player = $event->getPlayer();
        if(!$replay->isRunning() || !$replay->isReplayWorld($player->getWorld())){
            return;
        }
        switch ($player->getGamemode()) {
            case GameMode::SPECTATOR():
                $replay->addAction()->despawnEntityAction($player);
                break;
            default:
                $replay->addEntity($player);
        }
    }

    /**
     * @param EntityMoveEvent $event
     * @priority MONITOR
     */

    public function onEntityMove(EntityMoveEvent $event) {
        $replay = BattleCore::getInstance()->replaySystemRecorder->getReplay();
        if($replay->isRunning() && $replay->isReplayWorld($event->getEntity()->getWorld()) && !$event->getEntity() instanceof Player && !$event->getEntity() instanceof Painting){
            $replay->addAction()->entityMoveAction($event->getEntity());
        }
    }

    /**
     * @param SignChangeEvent $event
     * @priority MONITOR
     */

    public function onSignChange(SignChangeEvent $event) {
        $player = $event->getPlayer();
        $replay = BattleCore::getInstance()->replaySystemRecorder->getReplay();
        if($replay->isRunning() && $replay->isReplayWorld($player->getWorld()) && !$event->isCancelled()){
            $replay->addAction()->signChangeAction($event->getBlock(), $event->getNewText()->getLine(0), $event->getNewText()->getLine(1), $event->getNewText()->getLine(2), $event->getNewText()->getLine(3));
        }
    }

    /**
     * @param PlayerItemHeldEvent $event
     * @priority MONITOR
     */

    public function onItemHeld(PlayerItemHeldEvent $event) {
        $player = $event->getPlayer();
        $replay = BattleCore::getInstance()->replaySystemRecorder->getReplay();
        if($replay->isRunning() && $replay->isReplayWorld($player->getWorld()) && !$event->isCancelled()){
            if($event->getPlayer()->isSpectator()) {
                return;
            }
            $replay->addAction()->itemHeldAction($player);
        }
    }

    /**
     * @param EntityExplodeEvent $event
     * @priority MONITOR
     */

    public function onExplode(EntityExplodeEvent $event): void {
        $entity = $event->getEntity();
        $replay = BattleCore::getInstance()->replaySystemRecorder->getReplay();
        if($replay->isRunning() && $replay->isReplayWorld($entity->getWorld()) && !$event->isCancelled()) {
            $replay->addAction()->breakAction($entity->getWorld()->getBlock($event->getPosition()), true);
            foreach ($event->getBlockList() as $block) {
                $replay->addAction()->breakAction($block, true);
            }
        }
    }

    /**
     * @param InventoryTransactionEvent $event
     * @priority MONITOR
     */

    public function onTransaction(InventoryTransactionEvent $event) {
        $player = $event->getTransaction()->getSource();
        $replay = BattleCore::getInstance()->replaySystemRecorder->getReplay();
        if($replay->isRunning() && $replay->isReplayWorld($player->getWorld()) && !$event->isCancelled()){
            if($player->isSpectator()) {
                return;
            }
            $replay->addAction()->itemHeldAction($player);
        }
    }

    /**
     * @param LeavesDecayEvent $event
     * @priority MONITOR
     */

    public function onLeavesDecay(LeavesDecayEvent $event) {
        $replay = BattleCore::getInstance()->replaySystemRecorder->getReplay();
        if($replay->isRunning() && $replay->isReplayWorld($event->getBlock()->getPosition()->getWorld()) && !$event->isCancelled()){
            $replay->addAction()->breakAction($event->getBlock(), true);
        }
    }

    /**
     * @param BlockBurnEvent $event
     * @priority MONITOR
     */

    public function onBlockBurn(BlockBurnEvent $event) {
        $replay = BattleCore::getInstance()->replaySystemRecorder->getReplay();
        if($replay->isRunning() && $replay->isReplayWorld($event->getBlock()->getPosition()->getWorld()) && !$event->isCancelled()){
            $replay->addAction()->breakAction($event->getBlock(), true);
        }
    }

    /**
     * @param BlockUpdateEvent $event
     * @priority MONITOR
     */

    public function onBlockUpdate(BlockUpdateEvent $event) {
        $block = $event->getBlock();
        $replay = BattleCore::getInstance()->replaySystemRecorder->getReplay();
        if($replay->isRunning() && $replay->isReplayWorld($block->getPosition()->getWorld()) && !$event->isCancelled()){
            $replay->addAction()->onBlockUpdate($block);
        }
    }

    /**
     * @param BlockGrowEvent $event
     * @priority MONITOR
     */

    public function onBlockGrow(BlockGrowEvent $event) {
        $replay = BattleCore::getInstance()->replaySystemRecorder->getReplay();
        if($replay->isRunning() && $replay->isReplayWorld($event->getBlock()->getPosition()->getWorld()) && !$event->isCancelled()){
            $replay->addAction()->placeAction($event->getNewState());
        }
    }

    /**
     * @param EntityItemPickupEvent $event
     * @priority MONITOR
     */

    public function onItemPickup(EntityItemPickupEvent $event): void {
		$entity = $event->getEntity();
        $replay = BattleCore::getInstance()->replaySystemRecorder->getReplay();
        if($replay->isRunning() && $replay->isReplayWorld($entity->getWorld()) && !$event->isCancelled()){
            $replay->addAction()->despawnEntityAction($entity);
        }
    }

    /**
     * @param DataPacketReceiveEvent $event
     */

    public function dataPacket(DataPacketReceiveEvent $event): void {
        $player = $event->getOrigin()->getPlayer();
        $packet = $event->getPacket();
        $replay = BattleCore::getInstance()->replaySystemRecorder->getReplay();
        if($replay->isRunning() && $replay->isReplayWorld($player->getWorld())){
            if($event->getOrigin()->getPlayer()->isSpectator()) {
                return;
            }
            if($packet instanceof PlayerActionPacket) {
                $position = $packet->blockPosition;
                switch ($packet->action) {
                    case PlayerAction::START_BREAK: {
                        $breakTime = ceil($player->getWorld()->getBlock($position)->getBreakInfo()->getBreakTime($player->getInventory()->getItemInHand()) * 20);
                        if($breakTime <= 0) {
                            $breakTime = 1;
                        }
                        $replay->addAction()->levelEventAction($position, LevelEvent::BLOCK_START_BREAK, (int) (65535 / $breakTime));

                        $replay->addAction()->itemHeldAction($player);
                        break;
                    }
                    case PlayerAction::ABORT_BREAK: {}
                    case PlayerAction::STOP_BREAK: {
                        $replay->addAction()->levelEventAction($position, LevelEvent::BLOCK_STOP_BREAK, 0);

                        $replay->addAction()->itemHeldAction($player);
                        break;
                    }
                }
            }
        }
    }

    /**
     * @param QueryRegenerateEvent $event
     */

    public function onQuery(QueryRegenerateEvent $event): void {
        $replay = BattleCore::getInstance()->replaySystemRecorder->getReplay();
        if(!$replay->isRunning()) {
            return;
        }
        foreach ($replay->getWorld()->getEntities() as $entity) {
            $replay->addAction()->entityUpdateAction($entity);
        }
    }

    /**
     * @param BlockSpreadEvent $event
     * @priority MONITOR
     */

    public function onSpread(BlockSpreadEvent $event): void {
        /*$block = $event->getNewState();
        $replay = BattleCore::getInstance()->replaySystemRecorder->getReplay();
        if($replay->isRunning() && $replay->isReplayWorld($block->getLevel()) && !$event->isCancelled()){
            $replay->addAction()->placeAction($block);
        }*/
    }

    /*
     * Currently working on:
     *
     *
     *
     * Not finished Events:
     *
     * - BlockUpdateEvent
     * - PlayerChatEvent
     */
}