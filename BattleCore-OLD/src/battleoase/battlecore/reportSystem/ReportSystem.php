<?php


namespace battleoase\battlecore\reportSystem;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\utils\BPlugin;
use Closure;
use pocketmine\scheduler\ClosureTask;

class ReportSystem extends BPlugin
{
	public function __construct()
	{
		BattleCore::getInstance()->getMysqlConnection()->query("CREATE TABLE `Core`.`report_players` ( `id` INT NOT NULL AUTO_INCREMENT , `reported_player` VARCHAR(64) NOT NULL , `reported_by` VARCHAR(64) NOT NULL , `reason` VARCHAR(64) NOT NULL , `report_time` VARCHAR(64) NOT NULL , `server` VARCHAR(64) NOT NULL , `extraData` VARCHAR(64) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;");
	}
}