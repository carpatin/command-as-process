<?php

namespace LiveHappyCodeHard\CommandAsProcess;

/**
 * Models the environment in which a command executes.
 */
class CommandEnvironment
{

    /**
     * Stores the environment variables.
     *
     * @var array
     */
    private $variables;

    /**
     * Initializes variables container.
     *
     * @param array $variables
     */
    public function __construct(array $variables = [])
    {
        $this->variables = $variables;
    }

    /**
     * Adds a variable: a string value for a given name.
     *
     * @param string $name
     * @param string $value
     */
    public function addVariable($name, $value)
    {
        $this->variables[(string)$name] = (string)$value;
    }

    /**
     * Returns variables array.
     *
     * @return array
     */
    public function getVariables()
    {
        return $this->variables;
    }
}
