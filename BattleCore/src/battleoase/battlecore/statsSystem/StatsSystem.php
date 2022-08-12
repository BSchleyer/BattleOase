<?php


namespace battleoase\battlecore\statsSystem;


use battleoase\battlecore\BattleCore;
use pocketmine\entity\Skin;
use pocketmine\network\mcpe\convert\SkinAdapterSingleton;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class StatsSystem
{
    public function __construct() {}
    /**
     * @param string $game
     */
    public function createStatsTable(string $game)
    {
        BattleCore::getInstance()->getConnection()->query("CREATE TABLE Stats.{$game} ( `id` INT NOT NULL AUTO_INCREMENT , `player_name` VARCHAR(16) NOT NULL , `kills` INT(255) NOT NULL , `deaths` INT(255) NOT NULL , `elo` INT(255) NOT NULL , `wins` INT(255) NOT NULL , `loses` INT(255) NOT NULL, `date` DATE NOT NULL DEFAULT CURRENT_TIMESTAMP  , PRIMARY KEY (`id`, `player_name`)) ENGINE = InnoDB;");
    }

    private function editStatsProfile($name, string $game, int $kills = 0, int $deaths = 0, int $elo = 0, int $wins = 0, int $loses = 0)
    {
        BattleCore::getInstance()->getConnection()->query("INSERT INTO Stats.{$game} (player_name, kills, deaths, elo, wins, loses) VALUES ('$name', '$kills', '$deaths', '$elo', '$wins', '$loses');");
    }

    /**
     * @param $player
     * @param Skin $skin
     */
    public function saveSkin($player, Skin $skin)
    {
        if($player instanceof Player) {
            $name = $player->getName();
        } else {
            $name = $player;
        }
        $skinData = base64_encode(zlib_encode($skin->getSkinData(), ZLIB_ENCODING_DEFLATE));
        $capedata = base64_encode($skin->getCapeData());
        $geometryData = $skin->getGeometryData();
        $geometryName = $skin->getGeometryName();
        BattleCore::getInstance()->getConnection()->query("INSERT INTO Stats.Skins(`player_name`, `skin_data`, `cape_data`,`geometry_name`, `geometry_data`) VALUES ('$name', '$skinData', '$capedata', '$geometryName', '$geometryData')");
        BattleCore::getInstance()->getConnection()->query("UPDATE Stats.Skins SET `skin_data` = '$skinData' ,`cape_data` = '$capedata' , `geometry_name` = '$geometryName' ,`geometry_data` = '$geometryData' WHERE `player_name` = '$name'");
    }

    public static function getSkinNameFormat(string $name): string
    {
        return TextFormat::AQUA .str_replace("BATTLEOASE_", "§6§l", str_replace("BATTLEUNITY_", "§e§l", $name));
    }

	public function getSkin(string $name): Skin
	{
		$result = BattleCore::getInstance()->getConnection()->query("SELECT * FROM Stats.Skins WHERE player_name='$name'");
		while ($row = $result->fetch_assoc()) {
			return new Skin(uniqid(), zlib_decode(base64_decode($row['skin_data'])), $row['cape_data'], $row['geometry_name'], $row['geometry_data']);
		}
        return new Skin("", "", "", "", "");
	}

    public function getStatsTables()
    {
        $return = [];
        $query = BattleCore::getInstance()->getConnection()->query("SHOW TABLES IN Stats");
        if($query->num_rows > 0) {
            while ($row = $query->fetch_assoc()) {
                if($row["Tables_in_Stats"] == "Skins") continue;
                $return[] = $row["Tables_in_Stats"];
            }
        }
        return $return;
    }


    /**
     * @param $player
     * @param string $game
     */
    public function addKill($player, string $game)
    {
        if($player instanceof Player) {
            $player = $player->getName();
        }
        $this->editStatsProfile($player, $game, 1);
    }

    /**
     * @param $player
     * @param string $game
     */
    public function addDeath($player, string $game)
    {
        if($player instanceof Player) {
            $player = $player->getName();
        }
        $this->editStatsProfile($player, $game, 0, 1);
    }

    /**
     * @param $player
     * @param int $elo
     * @param string $game
     */
    public function addElo($player, int $elo, string $game)
    {
        if($player instanceof Player) {
            $player = $player->getName();
        }
        $this->editStatsProfile($player, $game, 0, 0, $elo);
    }


    /**
     * @param $player
     * @param int $elo
     * @param string $game
     */
    public function removeElo($player, int $elo, string $game)
    {
        if($player instanceof Player) {
            $player = $player->getName();
        }
        $this->editStatsProfile($player, $game, 0, 0, -$elo);
    }


    /**
     * @param $player
     * @param string $game
     */
    public function addLose($player, string $game)
    {
        if($player instanceof Player) {
            $player = $player->getName();
        }
        $this->editStatsProfile($player, $game, 0, 0, 0, 0, 1);
    }


    /**
     * @param string $game
     * @param int $limit
     * @return array
     */
    public function getTopWins(string $game, int $limit)
    {
        $return = [];
        $result = BattleCore::getInstance()->getConnection()->query("SELECT player_name, SUM(wins) AS wins FROM Stats.{$game} WHERE date BETWEEN (CURDATE() - INTERVAL 30 DAY) AND CURDATE() GROUP BY player_name ORDER BY SUM(elo) DESC LIMIT {$limit}");
        if($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $return[] = ["player_name" => $row["player_name"], "wins" => $row["wins"]];
            }
        }
        return $return;
    }

    /**
     * @param string $game
     * @param int $limit
     * @return array
     */
    public function getTopKills(string $game, int $limit)
    {
        $return = [];
        $result = BattleCore::getInstance()->getConnection()->query("SELECT player_name, SUM(kills) AS kills FROM Stats.{$game} WHERE date BETWEEN (CURDATE() - INTERVAL 30 DAY) AND CURDATE() GROUP BY player_name ORDER BY SUM(elo) DESC LIMIT {$limit}");
        if($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $return[] = ["player_name" => $row["player_name"], "kills" => $row["kills"]];
            }
        }
        return $return;
    }

    /**
     * @param string $game
     * @param int $limit
     * @return array
     */
    public function getTopElo(string $game, int $limit)
    {
        $return = [];

        $result = BattleCore::getInstance()->getConnection()->query("SELECT player_name, SUM(elo) AS elo FROM Stats.{$game} WHERE date BETWEEN (CURDATE() - INTERVAL 30 DAY) AND CURDATE() GROUP BY player_name ORDER BY SUM(elo) DESC LIMIT {$limit}");
        if($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $return[] = ["player_name" => $row["player_name"], "elo" => $row["elo"]];
            }
        }
        return $return;
    }


    public function getMonthlyTime()
    {
        return date('Y-m-d');
    }



    /**
     * @param string $game
     * @param int $limit
     * @return array
     */
    public function getElo($player, string $game, int $days = 30000)
    {
        if($player instanceof Player) {
            $player = $player->getName();
        }
        $result = BattleCore::getInstance()->getConnection()->query("SELECT SUM(elo) AS elo FROM Stats.{$game} WHERE player_name='$player' AND date BETWEEN (CURDATE() - INTERVAL $days DAY) AND CURDATE()");
        if($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                return $row["elo"];
            }
        }
        return "0";

    }

    /**
     * @param string $game
     * @param int $limit
     * @return array
     * Test
     */
    public function getKill($player, string $game, int $days = 30000)
    {
        if($player instanceof Player) {
            $player = $player->getName();
        }
        $result = BattleCore::getInstance()->getConnection()->query("SELECT SUM(kills) AS kills FROM Stats.{$game} WHERE player_name='$player' AND date BETWEEN (CURDATE() - INTERVAL $days DAY) AND CURDATE()");
        if($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                return $row["kills"];
            }
        }
        return "0";

    }

    /**
     * @param string $game
     * @param int $limit
     * @return array
     */
    public function getDeath($player, string $game, int $days = 30000)
    {
        if($player instanceof Player) {
            $player = $player->getName();
        }
        $result = BattleCore::getInstance()->getConnection()->query("SELECT SUM(deaths) AS deaths FROM Stats.{$game} WHERE player_name='$player' AND date BETWEEN (CURDATE() - INTERVAL $days DAY) AND CURDATE()");
        if($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                return $row["deaths"];
            }
        }
        return "0";

    }


    /**
     * @param string $game
     * @param int $limit
     * @return array
     */
    public function getLoses($player, string $game, int $days = 30000)
    {
        if($player instanceof Player) {
            $player = $player->getName();
        }
        $result = BattleCore::getInstance()->getConnection()->query("SELECT SUM(loses) AS loses FROM Stats.{$game} WHERE player_name='$player' AND date BETWEEN (CURDATE() - INTERVAL $days DAY) AND CURDATE()");
        if($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                return $row["loses"];
            }
        }
        return "0";

    }

    /**
     * @param string $game
     * @param int $limit
     * @return array
     */
    public function getWins($player, string $game, int $days = 30000)
    {
        if($player instanceof Player) {
            $player = $player->getName();
        }
        $result = BattleCore::getInstance()->getConnection()->query("SELECT SUM(wins) AS wins FROM Stats.{$game} WHERE player_name='$player' AND date BETWEEN (CURDATE() - INTERVAL $days DAY) AND CURDATE()");
        if($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                return $row["wins"];
            }
        }
        return "0";
    }

	/**
	 * Function saveHead
	 * @param string $path
	 * @param $pathOrImage
	 * @param int $height
	 * @param int $width
	 * @return void
	 */
	public function saveHead(string $path, $pathOrImage, int $height, int $width): void{
		if (is_string($pathOrImage)) {
			$pathOrImage = imagecreatefrompng($pathOrImage);
		}
		$body = imagecreatetruecolor($height, $width);
		imagefill($body, 0, 0, imagecolorallocatealpha($body, 0, 0, 0, 127));
		imagecolordeallocate($body, imagecolorallocate($body, 0, 0, 0));
		imagesavealpha($body, true);
		if ($height == 64 && $width == 64) {
			$src_xy = 8;
			$src_w = $src_h = 8;
			$hat_src_x = 40;
			$hat_src_y = 8;
		} else if ($height == 128 && $width == 128) {
			$rgb = imagecolorat($pathOrImage, 8, 8);
			$colors = imagecolorsforindex($pathOrImage, $rgb);
			if (!($colors["red"] == 0 && $colors["green"] == 0 && $colors["blue"] == 0 && $colors["alpha"] == 0)) {
				$src_xy = 8;
				$src_w = $src_h = 8;
				$hat_src_x = 40;
				$hat_src_y = 8;
			} else {
				$src_xy = 16;
				$src_w = $src_h = 16;
				$hat_src_x = 80;
				$hat_src_y = 16;
			}
		} else if ($height == 32 && $width == 64) {
			$src_xy = 16;
			$src_w = $src_h = 8;
			$hat_src_x = 40;
			$hat_src_y = 8;
		} else {
			$src_xy = 8;
			$src_w = $src_h = 8;
			$hat_src_x = 40;
			$hat_src_y = 8;
		}
		imagecopyresized($body, $pathOrImage, 0, 0, $src_xy, $src_xy, $height, $width, $src_w, $src_h);				//head
		if (!($height == 32 && $width == 64)) {
			imagecopyresized($body, $pathOrImage, 0, 0, $hat_src_x, $hat_src_y, $height, $width, $src_w, $src_h);	//hat
		}
		if (is_file($path)) {
			unlink($path);
		}
		imagepng($body, $path);
		@imagedestroy($body);
		@imagedestroy($pathOrImage);
	}


	/**
	 * Function fromSkinToImage
	 * @param Skin $skin
	 * @return false|resource
	 */
	public function fromSkinToImage(Skin $skin){
		return $this->toImage($skin->getSkinData(), $this->getHeigth($skin), $this->getWidth($skin));
	}

	public function getHeigth(Skin $skin): int{
		return  SkinAdapterSingleton::get()->toSkinData($skin)->getSkinImage()->getHeight();
	}

	public function getWidth(Skin $skin): int{
		return  SkinAdapterSingleton::get()->toSkinData($skin)->getSkinImage()->getWidth();
	}

	/**
	 * Function toImage
	 * @param string $data
	 * @param int $height
	 * @param int $width
	 * @return false|resource
	 */
	public  function toImage(string $data, int $height, int $width){
		$pixelarray = str_split(bin2hex($data), 8);
		$image = imagecreatetruecolor($width, $height);
		imagealphablending($image, false);//do not touch
		imagefill($image, 0, 0, imagecolorallocatealpha($image, 0, 0, 0, 127));
		imagesavealpha($image, true);
		$position = count($pixelarray) - 1;
		while (!empty($pixelarray)){
			$x = $position % $width;
			$y = ($position - $x) / $height;
			$walkable = str_split(array_pop($pixelarray), 2);
			$color = array_map(function ($val){ return hexdec($val); }, $walkable);
			$alpha = array_pop($color); // equivalent to 0 for imagecolorallocatealpha()
			$alpha = ((~((int)$alpha)) & 0xff) >> 1; // back = (($alpha << 1) ^ 0xff) - 1
			array_push($color, $alpha);
			imagesetpixel($image, $x, $y, imagecolorallocatealpha($image, ...$color));
			$position--;
		}
		return $image;
	}

}