<?php

namespace battleoase\battlecore\util;

use Closure;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Config;
use ReflectionFunction;

class AsyncExecutor {
    public static array $mysqlLoginData;
    public static array $taskClosures = [];

    public function __construct(){
        $config = new Config("/home/cloud/mysql.yml", Config::JSON, ["address" => "", "user" => "", "password" => ""]);

        self::$mysqlLoginData = [
            $config->get("address"),
            $config->get("user"),
            $config->get("password")
        ];
    }

    public static function submitAsyncTask(Closure $asyncClosure, ?Closure $syncClosure = null){
        $syncId = null;
        if($syncClosure !== null){
            $syncId = uniqid();
            self::$taskClosures[$syncId] = $syncClosure;
        }
        Server::getInstance()->getAsyncPool()->submitTask(new class($asyncClosure, $syncId) extends AsyncTask {
            private string $db;
            public function __construct(
                protected Closure $closure,
                protected ?string $syncId = null
            ){
                $this->db = json_encode(AsyncExecutor::$mysqlLoginData);
            }

            public function onRun(): void{
                $microtime = microtime(true);
                $reflection = new ReflectionFunction($this->closure);
                $mysqli = null;
                switch($reflection->getNumberOfParameters()){
                    case 0: {
                        $this->setResult(($this->closure)());
                        break;
                    }
                    default: {
                        $mysqli = mysqli_connect(...json_decode($this->db, true));
                        $this->setResult(($this->closure)($mysqli));
                    }
                }
                if($mysqli !== null){
                    if(count($mysqli->error_list) > 0){
                        var_dump($mysqli->error_list);
                    }
                    $mysqli->close();
                }
                $time = microtime(true) - $microtime;
                if($time > 1.0) {
                    echo "[Notice]: Async task took ".$time." seconds with closure having ".$reflection->getNumberOfParameters()." parameters\n";
                }
            }

            public function onCompletion(): void{
                if($this->syncId !== null){
                    $closure = AsyncExecutor::$taskClosures[$this->syncId];
                    $closure($this->getResult());
                    unset(AsyncExecutor::$taskClosures[$this->syncId]);
                }
            }
        });
    }
}