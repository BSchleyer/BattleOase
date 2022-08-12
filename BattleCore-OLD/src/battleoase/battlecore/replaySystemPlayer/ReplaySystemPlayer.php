<?php

namespace battleoase\battlecore\replaySystemPlayer;

use battleoase\battlecore\BattleCore;
use battleoase\battlecore\replaySystemPlayer\entities\HumanEntity;
use battleoase\battlecore\replaySystemPlayer\manager\Replay;
use battleoase\battlecore\replaySystemPlayer\scheduler\CustomEventTask;
use battleoase\battlecore\utils\BPlugin;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Human;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\world\World;

class ReplaySystemPlayer extends BPlugin
{
    /** @var Replay */
    private Replay $replay;
    
    public function __construct() {
        $this->replay = new Replay();

        new EventListener();

        BattleCore::getInstance()->getScheduler()->scheduleRepeatingTask(new CustomEventTask(), 1);
		BattleCore::getInstance()->getScheduler()->scheduleDelayedRepeatingTask(new class() extends Task {

            public function onRun(): void
            {
                if(count(BattleCore::getInstance()->getServer()->getOnlinePlayers()) == 0 ) {
					BattleCore::getInstance()->getServer()->shutdown();
                }
            }
        }, 600, 600);

		EntityFactory::getInstance()->register(HumanEntity::class, function (World $world, CompoundTag $nbt): HumanEntity {
			return new HumanEntity(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
		}, ['minecraft:human', 'human']);

    }


    /**
     * @return Replay
     */

    public function getReplay() : Replay {
        return $this->replay;
    }

    /**
     * @param $src
     * @param $dst
     */

    public function copymap($src, $dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->copymap($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    /**
     * @param $dirPath
     * @return bool
     */

    public function deleteDir($dirPath) {
        if (!is_dir($dirPath)) {
            return false;
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    /**
     * @param Item $item
     * @param string $tag
     * @param string $tagName
     * @return Item
     */

    public function addItemTag(Item $item, string $tag, string $tagName) : Item {
        $nbt = $item->getNamedTag();
        $nbt->setString($tagName, $tag);
        $item->setNamedTag($nbt);
        return $item;
    }

    /**
     * @param Item $item
     * @param string $tagName
     * @return bool
     */

    public function hasItemTag(Item $item, string $tagName) : bool {
        $nbt = $item->getNamedTag();
        return $nbt->hasTag($tagName, StringTag::class);
    }

    /**
     * @param Item $item
     * @param string $tagName
     * @return string
     */

    public function getItemTag(Item $item, string $tagName) : string {
        $nbt = $item->getNamedTag();
        return $nbt->getString($tagName);
    }

}