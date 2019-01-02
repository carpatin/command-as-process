<?php

namespace LiveHappyCodeHard\CommandAsProcess;

/**
 * Command run as a separate process.
 * Allows interaction through pipes or files with the run command.
 */
class CommandProcess
{

    /**
     * @var CommandDescriptor
     */
    private $commandDescriptor;

    /**
     * @var CommandEnvironment
     */
    private $commandEnvironment;

    /**
     * @var InputOutputConfiguration
     */
    private $ioConfiguration;

    /**
     * Flag for keeping started status.
     *
     * @var bool
     */
    private $isStarted;

    /**
     * The resource returned by system function that is used to address the child process.
     *
     * @var resource
     */
    private $processResource;

    /**
     * Container for input/output pipes open with the child.
     *
     * @var array
     */
    private $ioPipes;

    /**
     * Initializes instance properties.
     *
     * @param CommandDescriptor        $cmd
     * @param CommandEnvironment       $env
     * @param InputOutputConfiguration $ioConfig
     */
    public function __construct(CommandDescriptor $cmd, CommandEnvironment $env, InputOutputConfiguration $ioConfig)
    {
        $this->commandDescriptor = $cmd;
        $this->commandEnvironment = $env;
        $this->ioConfiguration = $ioConfig;
        $this->isStarted = false;
    }

    /**
     * Starts command execution.
     *
     * @return bool
     * @throws \Exception
     */
    public function open()
    {
        // Ensure command is executed only once
        if ($this->isStarted)
        {
            return false;
        }

        // Obtain command execution details
        $command = $this->commandDescriptor->getCommand();
        $workingDirectory = $this->commandDescriptor->getWorkingDirectory();
        $descriptorSpecification = $this->ioConfiguration->getDescriptorSpecification();
        $environmentVariables = $this->commandEnvironment->getVariables();

        // Call system function to create process executing the command
        $this->processResource =
            proc_open($command, $descriptorSpecification, $this->ioPipes, $workingDirectory, $environmentVariables);

        // Check for failure
        if ($this->processResource === false)
        {
            throw new \Exception('Failed to start the process');
        }

        $this->isStarted = true;

        return $this->isStarted;
    }

    /**
     * Closes unclosed pipes.
     */
    private function closePipes()
    {
        foreach ($this->ioPipes as $handle)
        {
            if (is_resource($handle))
            {
                fclose($handle);
            }
        }
    }

    /**
     * Waits for the process running the command to terminate and returns the
     * exit code.
     *
     * @return bool
     * @throws \Exception
     */
    public function close()
    {
        // Ensure process has been started first
        if (!$this->isStarted)
        {
            return false;
        }

        // Close any opened pipes
        $this->closePipes();

        // Wait for process to finish by itself, get the exit code and return it
        $status = $this->getStatus();
        $exitCode = proc_close($this->processResource);
        $actualExitCode = ($status->getStatus() == ProcessStatus::STATUS_RUNNING ? $exitCode : $status->getExitCode());

        return $actualExitCode;
    }

    /**
     * Terminate the process running the command immediately and returns whether the
     * process was successfully terminated.
     *
     * @param boolean $useKill FALSE by default, if TRUE is passed usses SIGKILL instead of SIGTERM
     *
     * @return bool
     * @throws \Exception
     */
    public function terminate($useKill = false)
    {
        // Ensure process has been started first
        if (!$this->isStarted)
        {
            return false;
        }

        $status = $this->getStatus();
        $isTerminated = true;
        if ($status->getStatus() == ProcessStatus::STATUS_RUNNING)
        {

            // Close any opened pipes
            $this->closePipes();

            // Choose signal to use
            $signal = SIGTERM;
            if ($useKill)
            {
                $signal = SIGKILL;
            }

            // Call system function in order to terminate process
            $isTerminated = proc_terminate($this->processResource, $signal);
        }

        return $isTerminated;
    }

    /**
     * Returns command process status.
     *
     * @return ProcessStatus
     * @throws \Exception
     */
    public function getStatus()
    {
        return new ProcessStatus($this->processResource);
    }

    /**
     * Returns write handle to process' STDIN
     *
     * @return resource|bool
     */
    public function getStdinHandle()
    {
        if (!$this->isStarted)
        {
            return false;
        }

        if (!$this->ioConfiguration->isStdinPipe())
        {
            return false;
        }

        return $this->ioPipes[InputOutputConfiguration::STDIN];
    }

    /**
     * Closes handle to process' STDIN.
     *
     * @return bool
     */
    public function closeStdinHandle()
    {
        if (!$this->isStarted)
        {
            return false;
        }
        if (!$this->ioConfiguration->isStdinPipe())
        {
            return false;
        }

        if (is_resource($this->ioPipes[InputOutputConfiguration::STDIN]))
        {
            fclose($this->ioPipes[InputOutputConfiguration::STDIN]);
        }

        return true;
    }

    /**
     * Returns read handle to process' STDOUT
     *
     * @return resource|bool
     */
    public function getStdoutHandle()
    {
        if (!$this->isStarted)
        {
            return false;
        }

        if (!$this->ioConfiguration->isStdoutPipe())
        {
            return false;
        }

        return $this->ioPipes[InputOutputConfiguration::STDOUT];
    }

    /**
     * Closes handle to process' STDOUT.
     *
     * @return boolean
     */
    public function closeStdoutHandle()
    {
        if (!$this->isStarted)
        {
            return false;
        }
        if (!$this->ioConfiguration->isStdoutPipe())
        {
            return false;
        }

        if (is_resource($this->ioPipes[InputOutputConfiguration::STDOUT]))
        {
            fclose($this->ioPipes[InputOutputConfiguration::STDOUT]);
        }

        return true;
    }

    /**
     * Returns read handle to process' STDERR
     *
     * @return resource|bool
     */
    public function getStderrHandle()
    {
        if (!$this->isStarted)
        {
            return false;
        }

        if (!$this->ioConfiguration->isStderrPipe())
        {
            return false;
        }

        return $this->ioPipes[InputOutputConfiguration::STDERR];
    }

    /**
     * Closes handle to process' STDERR.
     *
     * @return bool
     */
    public function closeStderrHandle()
    {
        if (!$this->isStarted)
        {
            return false;
        }
        if (!$this->ioConfiguration->isStderrPipe())
        {
            return false;
        }

        if (is_resource($this->ioPipes[InputOutputConfiguration::STDERR]))
        {
            fclose($this->ioPipes[InputOutputConfiguration::STDERR]);
        }

        return true;
    }
}
