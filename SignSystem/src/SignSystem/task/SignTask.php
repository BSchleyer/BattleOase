<?php

namespace SignSystem\task;

use pocketmine\block\BaseSign;
use pocketmine\block\utils\SignText;
use pocketmine\scheduler\Task;
use SignSystem\objects\GroupSign;
use SignSystem\provider\ServerProvider;
use SignSystem\SignSystem;

class SignTask extends Task {

    public function onRun(): void {
        foreach (SignSystem::getInstance()->getSignProvider()->getSigns() as $sign) {
            if($sign instanceof GroupSign) {
                /** @var BaseSign $block */
                if (($block = $sign->getPosition()->getWorld()->getBlock($sign->getPosition())) instanceof BaseSign) {
                    if ($sign->getFounder() !== null) {
                        if (isset(SignSystem::getInstance()->getAllServer()[$sign->getGroupName()])) {
                            if (in_array($sign->getFounder(), SignSystem::getInstance()->getAllServer()[$sign->getGroupName()])) {
                                if (!ServerProvider::isUsedServer($sign->getGroupName(), $sign->getFounder())) ServerProvider::addUsedServer($sign->getGroupName(), $sign->getFounder());
                                $block->setText(new SignText($sign->nextFormatIndex()));
                                $block->getPosition()->getWorld()->setBlock($block->getPosition(), $block);
                            } else {
                                if (ServerProvider::isUsedServer($sign->getGroupName(), $sign->getFounder())) ServerProvider::removeUsedServer($sign->getGroupName(), $sign->getFounder());
                                $block->setText(new SignText($sign->nextFormatIndex()));
                                $block->getPosition()->getWorld()->setBlock($block->getPosition(), $block);
                            }
                        } else {
                            if (ServerProvider::isUsedServer($sign->getGroupName(), $sign->getFounder())) ServerProvider::removeUsedServer($sign->getGroupName(), $sign->getFounder());
                            $sign->setFounder(null);
                        }
                    } else {
                        $freeServer = $this->getFreeServer($sign->getGroupName());
                        if ($freeServer !== "") {
                            ServerProvider::addUsedServer($sign->getGroupName(), $freeServer);
                            $sign->setFounder($freeServer);
                            $block->setText(new SignText($sign->nextFormatIndex()));
                            $block->getPosition()->getWorld()->setBlock($block->getPosition(), $block);
                        } else {
                            $block->setText(new SignText($sign->nextFormatIndex()));
                            $block->getPosition()->getWorld()->setBlock($block->getPosition(), $block);
                        }
                    }
                }
            }
        }
    }

    private function getFreeServer(string $groupName): ?string {
        $server = "";
        foreach (SignSystem::getInstance()->getAllServer() as $group => $servers) {
            if ($groupName !== $group) continue;
            if (is_array($servers)) {
                foreach ($servers as $serverName) {
                    if (!ServerProvider::isUsedServer($groupName, $serverName)) {
                        $server = $serverName;
                    }
                }
            }
        }
        return $server;
    }
}