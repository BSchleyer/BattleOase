<?php

declare(strict_types=1);

namespace battleoase\battlecore\utils;

use battleoase\battlecore\BattleCore;
use Closure;
use mysqli;
use pocketmine\scheduler\AsyncTask;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\utils\Config;
use ReflectionFunction;
use function count;
use function json_decode;
use function json_encode;
use function microtime;
use function mysqli_connect;
use function uniqid;
use function var_dump;

class AsyncExecutor {

    /**^
     * @param string $database
     * @param Closure $function
     * @param Closure|null $completeFunction
     */
    public static function submitMYSQLAsyncTask(string $database, Closure $function, ?Closure $completeFunction = null)
    {
        Server::getInstance()->getAsyncPool()->submitTask(
            new class($function, $completeFunction, $database) extends AsyncTask {


                public function __construct(Closure $function, ?Closure $completeFunction, string $database)
                {

                    $this->function = $function;
                    $this->completeFunction = $completeFunction;
                    $this->database = $database;

                }


                public function onRun(): void
                {
                    $function = $this->function;
                    $config = new Config("/home/cloud/mysql.yml", Config::YAML);
                    $host = $config->get("address");
                    $user = $config->get("username");
                    $passwd = $config->get("password");
                    $mysqli = new mysqli($host, $user, $passwd, $this->database);
                    $this->setResult($function($mysqli));
                    $mysqli->close();
                }

                public function onCompletion(): void
                {
                    $server = Server::getInstance();
                    try {
                        $completeFunction = $this->completeFunction;
                        if($completeFunction === null) return;
                        $completeFunction($server, $this->getResult());
                    } catch (\Exception $exception) {
                        $server->getLogger()->error($exception->getMessage());
                    }
                }
            });
    }


    /**
     * @param int $tick
     * @param Closure $closure
     */
    public static function submitClosureTask(int $tick, Closure $closure)
    {
        BattleCore::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask($closure), $tick);
    }
}