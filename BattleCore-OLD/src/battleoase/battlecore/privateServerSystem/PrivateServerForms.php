<?php


namespace battleoase\battlecore\privateServerSystem;


use battleoase\battlecore\privateServerSystem\api\PrivateServerAPI;
use ceepkev77\cloudapi\api\ConfigAPI;
use ceepkev77\cloudapi\CloudAPI;
use Frago9876543210\EasyForms\elements\Button;
use Frago9876543210\EasyForms\elements\Image;
use Frago9876543210\EasyForms\forms\MenuForm;
use pocketmine\block\StoneButton;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class PrivateServerForms
{

    public static function sendPrivateServerUi(Player $player): void
    {
        $buttons = [
            new Button("Create PrivateServer")
        ];
        if($player->hasPermission("privateserver.list.server.admin")) {
            $buttons[] = new Button("List PrivateServer\n§e(ONLY ADMIN)");
        }

        $player->sendForm(new MenuForm(
            "§6§lPrivateServer",
            "",
            $buttons,
            function (Player $player, Button $button): void {
                $str = explode("\n", $button->getText())[0];
                if (TextFormat::clean($str) === "Create PrivateServer") self::sendTemplateUi($player);
                if (TextFormat::clean($str) === "List PrivateServer\n(ONLY ADMIN)") self::sendPrivateServerUi($player);
                $player->sendMessage((TextFormat::clean($str)));
            }
        ));
    }

    public static function sendTemplateUi(Player $player): void
    {
        $buttons = [];
        foreach (CloudAPI::getInstance()->getCloudConfig()->get("groups") as $name => $value) {
            if(ConfigAPI::canBePrivateServer($name)) {
                $buttons[] = $name;
            }

        }
        $player->sendForm(new MenuForm(
            "§6§lPrivateServer",
            "",
            $buttons,
            function (Player $player, Button $button): void {
                $template = explode("\n", $button->getText())[0];
                PrivateServerAPI::startSevrer(TextFormat::clean($template), $player);
            }
        ));
    }

}