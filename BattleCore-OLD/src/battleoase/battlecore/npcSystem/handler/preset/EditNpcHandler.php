<?php


namespace battleoase\battlecore\npcSystem\handler\preset;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\friendSystem\database\Database;
use battleoase\battlecore\npcSystem\classes\CustomType;
use battleoase\battlecore\npcSystem\entities\CustomNPC;
use battleoase\battlecore\npcSystem\handler\NPCEventHandler;
use battleoase\battlecore\statsSystem\StatsSystem;
use Frago9876543210\EasyForms\elements\Button;
use Frago9876543210\EasyForms\elements\Dropdown;
use Frago9876543210\EasyForms\elements\Input;
use Frago9876543210\EasyForms\elements\Slider;
use Frago9876543210\EasyForms\forms\CustomForm;
use Frago9876543210\EasyForms\forms\CustomFormResponse;
use Frago9876543210\EasyForms\forms\MenuForm;
use pocketmine\entity\Entity;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class EditNpcHandler extends NPCEventHandler
{

    private static function sendEditRotation(Player $player, Entity $entity)
    {
        $player->sendForm(new CustomForm(
            "§8• §6§lBattleNPC §r§8•",
            [
                new Slider("yaw", 0.0, 360.0, 1.0, (int)$entity->getLocation()->getYaw())
            ],
            function (Player $player, CustomFormResponse $response) use ($entity): void {
                list($yaw) = $response->getValues();
                $entity->setRotation($yaw, 0);
            }));
    }

    private static function sendEditSize(Player $player, Entity $entity)
    {
        $player->sendForm(new CustomForm(
            "§r§8• §6§lBattleNPC §r§8•",
            [
                new Slider("size", 0.0, 10.0, 0.1, (int)$entity->getScale())
            ],
            function (Player $player, CustomFormResponse $response) use ($entity): void {
                list($scale) = $response->getValues();
                if($entity instanceof CustomNPC) {
                    $entity->setScale($scale);
                    $entity->saveNBT();
                }
            }));
    }

    private static function sendEditSkin(Player $player, Entity $entity){
        BattleCore::getInstance()->getConnection()->executeQuery("SELECT * FROM Skins",
            "Stats",
            function($result){
                $data = [];
                while($row = mysqli_fetch_assoc($result)){
                    $data[] = $row["player_name"];
                }
                return $data;
            },
            function($result, $extra){

                Server::getInstance()->getPlayerExact($extra["player"])->sendForm(new CustomForm(
                    "§r§8• §6§lBattleNPC §r§8•",
                    [
                        new Dropdown("Skins", $result),
                    ],
                    function (Player $player, CustomFormResponse $response) use ($extra): void {
                        list($playerName) = $response->getValues();
                        $result = BattleCore::getInstance()->getMysqlConnection()->query("SELECT * FROM Stats.Skins WHERE player_name = '{$playerName}'");
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $skin = new Skin(uniqid(), zlib_decode(base64_decode($row["skin_data"])), "", $row["geometry_name"],$row["geometry_data"]);
                                $entity = Server::getInstance()->getPlayerExact($extra["player"])->getWorld()->getEntity($extra["entity"]);
                                if($entity instanceof CustomNPC) {
                                    $entity->setSkin($skin);
                                    $entity->sendSkin(Server::getInstance()->getOnlinePlayers());
                                }

                            }
                        }
                    }));
            },
            ["player" => $player->getName(), "entity" => $entity->getId()]);
    }

    private static function sendEditCommand(Player $player, Entity $entity)
    {
        if ($entity instanceof CustomNPC) {
            $handler = $entity->getHandler();
            if($handler instanceof CommandExecutionHandler) {
                $command = $handler->getCommand();
                $player->sendForm(new CustomForm(
                    "§r§8• §6§lBattleNPC §r§8•",
                    [
                        new Input("Edit Command", "Command without slash", $command),
                    ],
                    function (Player $player, CustomFormResponse $response) use ($entity): void {
                        list($input) = $response->getValues();
                        $commandclass = new CommandExecutionHandler();
                        $commandclass->setCommand($input);
                        $entity->setHandler($commandclass);
                    }));
            }

        }
    }

    private static function sendEditName(Player $player, Entity $entity)
    {
        $player->sendForm(new CustomForm(
            "§r§8• §6§lBattleNPC §r§8•",
            [
                new Input("Nametag", "Nametag", $entity->getNameTag()),
            ],
            function (Player $player, CustomFormResponse $response) use ($entity): void {
                list($input) = $response->getValues();
                if(is_null($input)) {
                    $player->sendMessage("Input is Null");
                } else {
                    $entity->setNameTag(str_replace("{LINE}", "\n", $input));
                }
            }));
    }

    public function onHit(Entity &$entity, EntityDamageByEntityEvent &$event): bool
    {
        if ($entity instanceof CustomNPC) {
            $player = $event->getDamager();
            if ($player instanceof Player) {
            	if($player->hasPermission("admin")) {
					$handler = $entity->getHandler();
					$buttons = ["§7Name", "§7Rotation", "§7Skin", "§7Size"];
					if ($handler instanceof CommandExecutionHandler) {
						$buttons[] = "§7Add Command";
						$buttons[] = "§cDelete";
					} else {
						$buttons[] = "§cDelete";
					}
					$player->sendForm(new MenuForm(
						"§r§8• §6§lBattleNPC §r§8•",
						"",
						$buttons,
						function (Player $player, Button $button) use ($entity): void {
							$str = explode("\n", $button->getText())[0];
							if (TextFormat::clean($str) === "Name") self::sendEditName($player, $entity);
							if (TextFormat::clean($str) === "Rotation") self::sendEditRotation($player, $entity);
							if (TextFormat::clean($str) === "Skin") self::sendEditSkin($player, $entity);
							if (TextFormat::clean($str) === "Add Command") self::sendEditCommand($player, $entity);
							if (TextFormat::clean($str) === "Size") self::sendEditSize($player, $entity);
							if (TextFormat::clean($str) === "Delete") $entity->kill();
						}
					));
				}
            }
        }
        return false;
    }



}