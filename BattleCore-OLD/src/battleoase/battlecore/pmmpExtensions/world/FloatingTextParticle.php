<?php

namespace battleoase\battlecore\pmmpExtensions\world;

use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\SetActorDataPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\particle\FloatingTextParticle as PMFloatingTextParticle;

class FloatingTextParticle extends PMFloatingTextParticle {

    /** @var Vector3 */
    private Vector3 $position;

    /**
     * @param Vector3 $vector3
     * @param string $text
     * @param string $title
     */
    public function __construct(Vector3 $vector3, string $text, string $title = ""){
        $this->position = $vector3;
        parent::__construct($text, $title);
        $this->entityId = Entity::nextRuntimeId();
    }

    public function updateText(): void{
        $actorPacket = new SetActorDataPacket();
        $actorPacket->entityRuntimeId = $this->getEntityId();

        $dataPropertyManager = new EntityMetadataCollection();
        $dataPropertyManager->setString(EntityMetadataProperties::NAMETAG, $this->getTitle().($this->getText() !== "" ? "\n".$this->getText() : ""));
        $dataPropertyManager->setGenericFlag(EntityMetadataFlags::INVISIBLE, $this->isInvisible());

        $actorPacket->metadata = $dataPropertyManager->getAll();
        foreach(Server::getInstance()->getOnlinePlayers() as $player)
            $player->getNetworkSession()->sendDataPacket($actorPacket);
    }

    public function getPosition(): Vector3{
        return $this->position;
    }

    public function getEntityId(): int{
        return $this->entityId;
    }

    public function showToPlayer(Player $player): void{
        Server::getInstance()->getWorldManager()->getDefaultWorld()->addParticle($this->position, $this, [$player]);
    }
}