<?php

namespace LiveHappyCodeHard\CommandAsProcess;

/**
 * Configuration for input/output of the process the command runs in.
 */
class InputOutputConfiguration
{
    /*
     * Common file descriptors identifiers
     */
    const STDIN  = 0;
    const STDOUT = 1;
    const STDERR = 2;

    /**
     * Data structure containing the configuration
     *
     * @var array
     */
    private $configuration;

    /**
     * Initializes with default configuration
     */
    public function __construct()
    {
        // Create default configuration
        $this->configuration = [
            self::STDIN  => ['pipe', 'r'],
            self::STDOUT => ['pipe', 'w'],
            self::STDERR => ['pipe', 'w'],
        ];
    }

    /**
     * Configures the standard input of the command to be a pipe.
     *
     * @return InputOutputConfiguration
     */
    public function setStdinAsPipe()
    {
        $this->configuration[self::STDIN] = ['pipe', 'r'];

        return $this;
    }

    /**
     * Configures the standard input of the command to be a file, path must be provided.
     *
     * @param string $path
     *
     * @return InputOutputConfiguration
     */
    public function setStdinAsFile($path)
    {
        $this->configuration[self::STDIN] = ['file', $path, 'r'];

        return $this;
    }

    /**
     * Returns whether the standard input is configured as pipe.
     *
     * @return boolean
     */
    public function isStdinPipe()
    {
        return reset($this->configuration[self::STDIN]) == 'pipe';
    }

    /**
     * Configures the standard output of the command to be a pipe.
     *
     * @return InputOutputConfiguration
     */
    public function setStdoutAsPipe()
    {
        $this->configuration[self::STDOUT] = ['pipe', 'w'];

        return $this;
    }

    /**
     * Configures the standard output of the command to be a file, path must be provided.
     *
     * @param string $path
     *
     * @return InputOutputConfiguration
     */
    public function setStdoutAsFile($path)
    {
        $this->configuration[self::STDOUT] = ['file', $path, 'w'];

        return $this;
    }

    /**
     * Returns whether the standard output is configured as pipe.
     *
     * @return boolean
     */
    public function isStdoutPipe()
    {
        return reset($this->configuration[self::STDOUT]) == 'pipe';
    }

    /**
     * Configures the standard error of the command to be a pipe.
     *
     * @param string $mode
     *
     * @return InputOutputConfiguration
     */
    public function setStderrAsPipe($mode = 'w')
    {
        $this->configuration[self::STDERR] = ['pipe', $mode];

        return $this;
    }

    /**
     * Configures the standard error of the command to be a file, path must be provided.
     *
     * @param string $path
     * @param string $mode
     *
     * @return InputOutputConfiguration
     */
    public function setStderrAsFile($path, $mode = 'w')
    {
        $this->configuration[self::STDERR] = ['file', $path, $mode];

        return $this;
    }

    /**
     * Returns whether the standard error is configured as pipe.
     *
     * @return boolean
     */
    public function isStderrPipe()
    {
        return reset($this->configuration[self::STDERR]) == 'pipe';
    }

    /**
     * Returns full configuration.
     *
     * @return array
     */
    public function getDescriptorSpecification()
    {
        return $this->configuration;
    }
}
