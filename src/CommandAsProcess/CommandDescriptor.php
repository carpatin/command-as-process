<?php

namespace LiveHappyCodeHard\CommandAsProcess;

/**
 * Models a command to be executed in a child process.
 */
class CommandDescriptor
{

    /**
     * The command to execute
     *
     * @var string
     */
    private $command;

    /**
     * The directory path where to execute de command in
     *
     * @var string|null
     */
    private $workingDirectory;

    /**
     * Initializes the descriptor's properties.
     *
     * @param string      $command
     * @param string|null $workingDirectory
     */
    public function __construct($command, $workingDirectory = null)
    {

        // Fix for PHP running command with sh
        // /bin/sh -c CMD will fork sh and then exec CMD.
        // /bin/sh -c exec CMD will NOT fork and only executes CMD.
        if (strpos($command, 'exec') !== 0)
        {
            $command = 'exec '.$command;
        }

        $this->command = $command;
        $this->workingDirectory = $workingDirectory;
    }

    /**
     * Returns command string.
     *
     * @return string
     */
    function getCommand()
    {
        return $this->command;
    }

    /**
     * Returns working directory if was provided, otherwise returns NULL.
     *
     * @return string|NULL
     */
    function getWorkingDirectory()
    {
        return $this->workingDirectory;
    }
}
