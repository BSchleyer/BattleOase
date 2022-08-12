<?php


namespace battleoase\battlecore\npcSystem\commands;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\npcSystem\caches\TypeCache;
use battleoase\battlecore\npcSystem\classes\NPCBuilder;
use battleoase\battlecore\npcSystem\handler\preset\CommandExecutionHandler;
use battleoase\battlecore\npcSystem\handler\preset\MessageHandler;
use MongoDB\Driver\Exception\CommandException;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Skin;
use pocketmine\player\Player;

class NpcCommand extends Command
{

    public function __construct()
    {
        parent::__construct("npc", "NPCs Command", "/npc");
        $this->setPermission("npc.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player){
            if ($sender->hasPermission($this->getPermission())){
                if (isset($args[0]) and isset($args[1])){
                    switch ($args[0]) {
                        case "upload":
                            $path = "/home/cloud/data/npcsystem/geos/" . $args[1] . "/" . $args[1] . ".png";
                            $size = getimagesize($path);
                            $img = @imagecreatefrompng($path);
                            $skinbytes = "";
                            for ($y = 0; $y < $size[1]; $y++) {
                                for ($x = 0; $x < $size[0]; $x++) {
                                    $colorat = @imagecolorat($img, $x, $y);
                                    $a = ((~((int)($colorat >> 24))) << 1) & 0xff;
                                    $r = ($colorat >> 16) & 0xff;
                                    $g = ($colorat >> 8) & 0xff;
                                    $b = $colorat & 0xff;
                                    $skinbytes .= chr($r) . chr($g) . chr($b) . chr($a);
                                }
                            }
                            @imagedestroy($img);
                            $customSkin = new Skin(uniqid(), $skinbytes, "", "geometry." . $args[1], file_get_contents("/home/cloud/data/npcsystem/geos/" . $args[1] . "/geometry.json"));
                            BattleCore::getInstance()->statsSystem->saveSkin("BATTLEOASE_" . $args[1], $customSkin);
                            $sender->sendMessage(BattleCore::getPrefix() . "§aDone!");
                            break;
                    }


                    switch ($args[1]){
                        case "command":
                            $value = $args[2];
                            $handler = new CommandExecutionHandler();
                            $handler->setCommand($value);
                            $builder = new NPCBuilder();
                            $entity = $builder->setType(TypeCache::get($sender->getName()))
                                ->setPosition($sender->getLocation())
                                ->setName($args[0])
                                ->setHandler($handler)
                                ->build();
                            $entity->spawnToAll();
                            break;
                        case "message":
                            $value = $args[2];
                            $handler = new MessageHandler();
                            $handler->setMessage($value);
                            $builder = new NPCBuilder();
                            $entity = $builder->setType(TypeCache::get($sender->getName()))
                                ->setPosition($sender->getLocation())
                                ->setName($args[0])
                                ->setHandler($handler)
                                ->build();
                            $entity->spawnToAll();
                            break;
                        case "void":
                            $builder = new NPCBuilder();
                            $entity = $builder->setType(TypeCache::get($sender->getName()))
                                ->setPosition($sender->getLocation())
                                ->setName($args[0])
                                ->build();
                            $entity->spawnToAll();
                            break;

                    }
                }
            }
        }
    }
}