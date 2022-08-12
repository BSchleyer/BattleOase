<?php


namespace battleoase\battlecore\databaseAPI\task;


use battleoase\battlecore\databaseAPI\Connection;
use Closure;
use mysqli;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class InsertionTask extends AsyncTask
{

    /** @var ?Closure */
    private ?Closure $action;

    /** @var String */
    private string $query;

    /** @var Connection */
    private Connection $connection;

    /** @var String */
    private string $db;
    /** @var array */
    private array $extra_data;

    /**
     * InsertionTask constructor.
     * @param string $query
     * @param Connection $connection
     * @param string $database
     * @param Closure|null $closure
     * @param array $extra_data
     */
    public function __construct(string $query, Connection $connection, string $database, ?Closure $closure, array $extra_data)
    {

        $this->action = $closure;
        $this->db = $database;
        $this->query = $query;
        $this->connection = $connection;
        $this->extra_data = $extra_data;
    }


    public function onRun(): void
    {
        $db = new mysqli($this->connection->host, $this->connection->user, $this->connection->password, $this->db);
        $s = $db->prepare($this->query);
        $s->execute();
        $this->setResult($s->get_result());
        if (!empty($db->error_list)) {
            $this->setResult($db->error_list);
        }
        $db->close();
    }


    public function onCompletion(): void
    {
        if (is_array($this->getResult())) {
            var_dump($this->getResult());
            return;
        }
        if ($this->action !== null) {
            $action = $this->action;
            $action($this->getResult(), $this->extra_data);
        }
        parent::onCompletion();
    }

}