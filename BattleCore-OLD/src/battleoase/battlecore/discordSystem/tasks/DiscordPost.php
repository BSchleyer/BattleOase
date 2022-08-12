<?php


namespace battleoase\battlecore\discordSystem\tasks;


use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class DiscordPost extends AsyncTask{

	private string $url;
	private string $content;

	public function __construct(String $url, String $content){
		$this->url = $url;
		$this->content = $content;
	}

	public function onRun(): void{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->url);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $this->content);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		$response = curl_exec($curl);
		$curlerror = curl_error($curl);

		if ($curlerror != '') {
			$error = $curlerror;
		} else if (curl_getinfo($curl, CURLINFO_HTTP_CODE) != 204) {
			$response = '';
		}

		$this->setResult($response);
	}

	public function onCompletion(): void{
	}
}