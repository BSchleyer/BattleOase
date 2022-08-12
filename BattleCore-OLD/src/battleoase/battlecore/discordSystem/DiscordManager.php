<?php

namespace battleoase\battlecore\discordSystem;

use battleoase\battlecore\discordSystem\tasks\DiscordPost;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class DiscordManager
{
	public static function postWebhook(string $url, string $content, string $username, array $embed = []): void{
		$data = [
			"username" => $username,
			"content" => $content
		];
		if(!empty($embed)){
			$data["embeds"] = $embed;
			unset($data["content"]);
		}else{
			$msg = $data["content"];
			$msg = str_replace("@everyone", "(@)everyone", $msg);
			$msg = str_replace("@here", "(@)here", $msg);
			$data["content"] = $msg;
		}
		$con = json_encode($data);
		$post = new DiscordPost("https://discordapp.com/api/webhooks/" . $url, $con);
		Server::getInstance()->getAsyncPool()->submitTask($post);
	}

	/**public static function sendProxyLogin(Player $player): void {
		$webhook = "935938638057984011/eM8KTo32z4_6wPAU-KfywjsM3yfEAn1Ly-NATO5I_vp3iEN9mUzA0vgjbmEmXRzvflfr";
		$p = "**Player:** " . $player->getName();

		DiscordManager::postWebhook($webhook, "", "Proxy", [
			[
				"color" => 0xFF0004,
				"title" => "Log of a Proxy-Join",
				"description" => $p
			]
		]);
	}**/
}