<?php

namespace battleoase\battlecore\externalPluginLoader\classes;

class GitRepositoryQueue
{
    private $repos = [];
    private $mapping = [];

    /**
     * @param GitRepository $repository
     */
    public function enqueue(GitRepository $repository): void
    {
        $this->repos[$repository->__toString()] = $repository->getPriority();
        $this->mapping[$repository->__toString()] = &$repository;
    }

    /**
     * @param string $key
     */
    public function dequeue(string $key): void
    {
        unset($this->repos[$key]);
        unset($this->mapping[$key]);
    }

    /**
     *
     */
    public function order(): void
    {
        asort($this->repos);
    }

    /**
     * @return GitRepository|null
     */
    public function getNext(): ?GitRepository
    {
        if (count($this->repos) == 0) return null;
        $key = array_keys($this->repos)[count($this->repos) - 1];
        return $this->mapping[$key] ?? null;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return count($this->repos) == 0;
    }
}