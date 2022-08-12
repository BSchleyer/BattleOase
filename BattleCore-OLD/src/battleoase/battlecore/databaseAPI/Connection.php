<?php


namespace battleoase\battlecore\databaseAPI;


use battleoase\battlecore\BattleCore;
use battleoase\battlecore\databaseAPI\task\InsertionTask;
use battleoase\battlecore\databaseAPI\task\SelectorTask;
use Closure;
use pocketmine\Server;

class Connection {

    public string $host;
    public string $user;
    public string $password;

    /**
     * Connection constructor.
     * @param string $host
     * @param string $user
     * @param string $password
     */
    public function __construct(String $host = "5.181.151.62", String $user = "admin",String $password = "HvAKbrjtsEAt8XX9nWH7rKyzbNhr54yRYKuBYTWg3fxzpESg8GeMvu9tDyVcSLaT")
    {
        $this->host = BattleCore::getDataConfig()->get("address");
        $this->user = BattleCore::getDataConfig()->get("username");
        $this->password = BattleCore::getDataConfig()->get("password");
    }

    /**
     * @param String $query
     * @param String $database
     * @param Closure|null $datahandler
     * @param Closure|null $action
     * @param array $data
     * @param Closure ...$closures
     */
    public function executeQuery(String $query, String $database, Closure $datahandler = null, Closure $action = null, array $data = [], Closure ...$closures): void
    {
        BattleCore::getInstance()->getServer()->getAsyncPool()->submitTask(new SelectorTask($query, $this, $datahandler, $action, $database , $data, $closures));
    }


    /**
     * @param String        $query
     * @param String        $database
     * @param Closure|null $action
     * @param array         $extra_data
     */
    public function execute(String $query, String $database, ?Closure $action, array $extra_data = []): void
    {
        Server::getInstance()->getAsyncPool()->submitTask(new InsertionTask($query, $this, $database, $action, $extra_data) );
    }

}