<?php


namespace battleoase\battlecore\databaseAPI\task;


use battleoase\battlecore\databaseAPI\Connection;
use Closure;
use mysqli;
use mysqli_result;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class SelectorTask extends AsyncTask
{

    /** @var Closure */
    private Closure $action;

    /** @var String */
    private string $query;

    /** @var Connection */
    private Connection $connection;

    /** @var String */
    private string $db;

    /** @var Closure */
    private Closure $handledata;

    /** @var array */
    private array $extra_data;

    /** @var Closure[] */
    private array $closures;

    /**
     * SelectorTask constructor.
     * @param String $query
     * @param Connection $connection
     * @param Closure $handledata
     * @param Closure $action
     * @param String $database
     * @param array $extra_data
     * @param array $closures
     */
    public function __construct(String $query, Connection $connection, Closure $handledata, Closure $action, String $database, array $extra_data = [], array $closures = [])
    {

        $this->action = $action;
        $this->db = $database;
        $this->query = $query;
        $this->handledata = $handledata;
        $this->connection = $connection;
        $this->extra_data = $extra_data;
        $this->closures = $closures;
    }


    public function onRun(): void
    {

        $db = new mysqli($this->connection->host, $this->connection->user, $this->connection->password, $this->db);
        $stmt = $db->prepare($this->query);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result instanceof mysqli_result) {
            if ( $this->handledata === null) {
                $this->setResult($result);
            } else {
                $a = $this->handledata;
                $this->setResult($a($result));
            }
        }
        $result->close();
        $db->close();
    }


    public function onCompletion(): void
    {
    	$server = Server::getInstance();
        $action = $this->action;
        $action($this->getResult(), $this->extra_data);
        foreach ($this->closures as $closure) {
            $closure($server, $this->getResult(), $this->extra_data);
        }
    }
}
