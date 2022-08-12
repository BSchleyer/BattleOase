<?php

namespace battleoase\battlecore\player\object;

use battleoase\battlecore\BattlePlayer;
use pocketmine\network\mcpe\protocol\BossEventPacket;

class Bossbar {
    private string $title = "";
    private string $subTitle = "";

    private float $healthPercent = 0.0;
    private int $color = 0;

    private bool $seeing = false;

    public function __construct(
        private BattlePlayer $player
    ){}

    public function getFullTitle(): string{
        $text = $this->title;
        if(!empty($this->subTitle)) $text .= "\n\n" . $this->subTitle;
        return mb_convert_encoding($text, "UTF-8");
    }

    public function show(): void{
        if($this->seeing) return;
        $this->seeing = true;
        $pk = BossEventPacket::show($this->player->getId(), $this->getFullTitle(), $this->getPercentage());
        $this->player->getNetworkSession()->sendDataPacket($pk);
    }

    public function hide(): void{
        if(!$this->seeing) return;
        $this->seeing = false;
        $pk = BossEventPacket::hide($this->player->getId());
        $this->player->getNetworkSession()->sendDataPacket($pk);
    }

    public function getPercentage(): float{
        return $this->healthPercent;
    }

    public function setPercentage(float $percentage): self{
        $this->healthPercent = (float)min(1.0, max(0.0, $percentage));
        $pk = BossEventPacket::healthPercent($this->player->getId(), $this->getPercentage());
        $this->player->getNetworkSession()->sendDataPacket($pk);
        return $this;
    }

    public function getTitle(): string{
        return $this->title;
    }

    public function getSubTitle(): string{
        return $this->subTitle;
    }

    public function setTitle(string $title): self{
        $this->title = $title;
        $this->updateTitle();
        return $this;
    }

    public function setSubTitle(string $subTitle): self{
        $this->subTitle = $subTitle;
        $this->updateTitle();
        return $this;
    }

    private function updateTitle(): void{
        $pk = BossEventPacket::title($this->player->getId(), $this->getFullTitle());
        $this->player->getNetworkSession()->sendDataPacket($pk);
    }

    private function updateTexture(): void {
        $pk = new BossEventPacket();
        $pk->bossActorUniqueId = $this->player->getId();
        $pk->eventType = BossEventPacket::TYPE_TEXTURE;
        $pk->color = $this->getColor();
        $pk->overlay = 0;
        $this->player->getNetworkSession()->sendDataPacket($pk);
    }

    /**
     * 0-6: (PINK, BLUE, RED, GREEN, YELLOW, PURPLE, WHITE)
     */
    public function getColor(): int{
        return $this->color;
    }

    public function setColor(int $color): self{
        $this->color = $color;
        $this->updateTexture();
        return $this;
    }

}