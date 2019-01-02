<?php

namespace LiveHappyCodeHard\CommandAsProcess;

/**
 * Command process status. Provides information about a command process from a
 * process resource.
 */
class ProcessStatus
{

    /**
     * The command string that was rexecuted
     *
     * @var string
     */
    private $command;

    /**
     * The Process ID of the command process
     *
     * @var int
     */
    private $pid;

    const STATUS_RUNNING  = 'running';
    const STATUS_SIGNALED = 'signaled';
    const STATUS_STOPPED  = 'stopped';

    /**
     * Status of the process: running, signaled or stopped
     *
     * @var string
     */
    private $status;

    /**
     * The exit code of the process.
     * Not NULL when status != STATUS_RUNNING
     *
     * @var int|null
     */
    private $exitCode;

    /**
     * The signal that was the reason for the process termination or stop.
     * Not NULL when status != STATUS_RUNNING
     *
     * @var int|null
     */
    private $reasonSignal;

    /**
     * Reads process info and interprets them.
     *
     * @param resource $processResource
     *
     * @throws \Exception
     */
    public function __construct($processResource)
    {
        if (!is_resource($processResource))
        {
            throw new \Exception('Constructor argument is not a resource');
        }

        $info = proc_get_status($processResource);

        if ($info === false)
        {
            throw new \Exception('Could not get process information');
        }

        $this->interpretProcessStatus($info);
    }

    /**
     * Interprets the status returned from platform function call.
     *
     * @param array $info
     */
    private function interpretProcessStatus($info)
    {
        $this->command = $info['command'];
        $this->pid = $info['pid'];
        if ($info['running'])
        {
            $this->status = self::STATUS_RUNNING;
        } elseif ($info['signaled'])
        {
            $this->status = self::STATUS_SIGNALED;
        } elseif ($info['stopped'])
        {
            $this->status = self::STATUS_STOPPED;
        }

        if ($this->status != self::STATUS_RUNNING)
        {
            $this->exitCode = $info['exitcode'];
        }

        if ($this->status == self::STATUS_SIGNALED)
        {
            $this->reasonSignal = $info['termsig'];
        }

        if ($this->status == self::STATUS_STOPPED)
        {
            $this->reasonSignal = $info['stopsig'];
        }
    }

    /**
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @return int
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * @return string One of the STATUS_* constants.
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return int|null
     */
    public function getExitCode()
    {
        return $this->exitCode;
    }

    /**
     * @return int|null
     */
    public function getReasonSignal()
    {
        return $this->reasonSignal;
    }
}
