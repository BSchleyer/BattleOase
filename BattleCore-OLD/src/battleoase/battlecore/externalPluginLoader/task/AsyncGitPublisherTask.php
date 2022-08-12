<?php


namespace battleoase\battlecore\externalPluginLoader\task;


use battleoase\battlecore\externalPluginLoader\classes\GitRepository;
use battleoase\battlecore\externalPluginLoader\classes\GitRepositoryQueue;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class AsyncGitPublisherTask extends AsyncTask
{
    /** @var string */
    public $auth;

    /** @var GitRepository[] */
    public $repositories;

    /** @var GitRepository[] */
    public $repositoryList = [];

    public $path;

    public function __construct(array $ar, $auth, string $path)
    {
        $this->path = $path;
        $this->repositories = serialize($ar);
        $this->auth = $auth;
    }

    public function onRun()
    {
        $token = explode(":", $this->auth)[1];
        $res = [];
        $this->repositories = unserialize($this->repositories);
        $queue = new GitRepositoryQueue();
        foreach ($this->repositories as $repository) {

            $queue->enqueue($repository);
        }
        $queue->order();
        while (!$queue->isEmpty()) {
            $repo = $queue->getNext();
            if ($this->downloadRepository($repo, $token)) {
                $res[] = $repo->getName();
            }
            $queue->dequeue($repo->__toString());
        }
        $this->setResult($res);
    }

    public function onCompletion(): void
    {
        foreach ($this->getResult() as $plugin) {
            Server::getInstance()->getPluginManager()->loadPlugins($plugin . ".phar");
            Server::getInstance()->getPluginManager()->enablePlugin(Server::getInstance()->getPluginManager()->getPlugin($plugin));
        }
    }

    public function downloadRepository(GitRepository $repository, string $auth_token): bool
    {
        $curl = curl_init("");
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 YaBrowser/19.9.3.314 Yowser/2.5 Safari/537.36',
        ]);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_URL, "https://api.github.com/repos/" . $repository->getAuthor() . "/" . $repository->getRepo() . "/releases/latest?access_token=" . $auth_token);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($curl);
        if ($res == false) {
            curl_close($curl);
            return false;
        }
        $co = json_decode($res, true);
        if (isset($co["id"])) {
            // Valid Repository Payload
            foreach ($co["assets"] as $asset) {
                if ($asset["name"] == $repository->getName() . ".phar") {
                    shell_exec("cd $this->path && curl -LJOs -H \"Accept: application/octet-stream\" \"https://api.github.com/repos/{$repository->getAuthor()}/{$repository->getRepo()}/releases/assets/" . $asset["id"] . "?access_token=$auth_token\"");
                    if (file_exists($this->path . $repository->getName() . ".phar")) {
                        var_dump("Download of " . $repository->getName() . " successfull!");
                        return true;
                    }
                }
            }
        }
        return false;
    }
}