<?php

namespace Cloud\command;

use Cloud\command\impl\CreateCommand;
use Cloud\command\impl\DispatchCommand;
use Cloud\command\impl\ExitCommand;
use Cloud\command\impl\HelpCommand;
use Cloud\command\impl\KickCommand;
use Cloud\command\impl\ListCommand;
use Cloud\command\impl\RemoveCommand;
use Cloud\command\impl\SaveCommand;
use Cloud\command\impl\SendCommand;
use Cloud\command\impl\StartCommand;
use Cloud\command\impl\StopCommand;
use Cloud\utils\CloudLogger;

class CommandManager {

    private static self $instance;

    /** @var Command[] */
    private array $commands = [];

    public function __construct() {
        self::$instance = $this;
        $this->registerCommand(new HelpCommand("help", "Get a list of all commands", "help", ["?"]));
        $this->registerCommand(new ExitCommand("exit", "Stop the cloud", "exit", ["shutdown"]));
        $this->registerCommand(new CreateCommand("create", "Create a template", "create <proxy|server> <name>", []));
        $this->registerCommand(new RemoveCommand("remove", "Remove a template", "remove <name>", []));
        $this->registerCommand(new StartCommand("start", "Start a server", "start <template> [count]", []));
        $this->registerCommand(new StopCommand("stop", "Stop a server", "stop <template|server|all>", []));
        $this->registerCommand(new DispatchCommand("dispatch", "Send a command to a server", "dispatch <server> <command>", ["execute"]));
        $this->registerCommand(new SaveCommand("save", "Save a server", "save <server>", []));
        $this->registerCommand(new ListCommand("list", "List of all templates and servers", "list [servers|templated|players]", []));
        $this->registerCommand(new KickCommand("kick", "Kick a player", "kick <player> [reason]", []));
        $this->registerCommand(new SendCommand("send", "Send a message to a player", "send <player> <message|title|popup|tip|actionbar> <message>", ["sendmsg"]));
    }

    public function registerCommand(Command $command) {
        if (!isset($this->commands[$command->getName()])) {
            $this->commands[$command->getName()] = $command;
        }
    }

    public function executeCommand(string $line): bool {
        $lines = explode(" ", $line);
        $commandName = $lines[0];
        $command = $this->getCommandByName($commandName);
        if ($command !== null) {
            unset($lines[0]);
            $command->execute(array_values($lines));
            return true;
        }
        return false;
    }

    public function getCommandByName(string $name): ?Command {
        foreach ($this->commands as $command) {
            if (strtolower($command->getName()) == strtolower($name)) return $command;
            else if (in_array($name, $command->getAliases())) return $command;
        }
        return null;
    }

    public function handleInput(string $input) {
        if (!$this->executeCommand($input)) {
            CloudLogger::getInstance()->error("The command doesn't exists!");
        }
    }

    /** @return Command[] */
    public function getCommands(): array {
        return $this->commands;
    }

    public static function getInstance(): CommandManager {
        return self::$instance;
    }
}