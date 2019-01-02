<?php

require '../vendor/autoload.php';

use LiveHappyCodeHard\CommandAsProcess\CommandDescriptor;
use LiveHappyCodeHard\CommandAsProcess\CommandEnvironment;
use LiveHappyCodeHard\CommandAsProcess\CommandProcess;
use LiveHappyCodeHard\CommandAsProcess\InputOutputConfiguration;

/*
 * A CommandProcess needs three parameters to create:
 * - a CommandDescriptor - basically the CLI command you want to run in the child process
 * - a CommandEnvironment - basically a wrapper over a map of environment variables passed to the child process
 * - an InputOutputConfiguration - the object in charge of the IO between the parent and the child processes
 */

// Create the descriptor for the command we want to run in a child process
$cmd = new CommandDescriptor('php child.php');

// Create the environment, showcasing here the passing of an environment variable for the child
$env = new CommandEnvironment(['PROCESS_WITH' => 'strtoupper']);

// Create the IO configuration object - no further changes on the config as we are going to use pipes, which is default
$io = new InputOutputConfiguration();


// Finally create the command process and run it in a child process
$command = new CommandProcess($cmd, $env, $io);
$command->open();


/*
 * We are going to handle now the IO with the child process running the command
 */

// Obtain STDIN of command
$in = $command->getStdinHandle();

// Some dummy data to pipe into the child process
$s1 = 'mary had a little lamb'.PHP_EOL;
$s2 = 'the brown fox jumps over the lazy dog'.PHP_EOL;
$sentBytesCount = strlen($s1) + strlen($s2);

print 'INPUT FROM PARENT'.PHP_EOL;
print $s1.$s2.PHP_EOL;

// Pass the data to child by writing the bytes to the standard input handle
fwrite($in, $s1);
fwrite($in, $s2);

// Closing the standard input handle is needed! Unless it runs for ever.
$command->closeStdinHandle();

print 'OUPUT FROM CHILD'.PHP_EOL;
// Read from child processed data
$out = $command->getStdoutHandle();
$total = 0;
do
{
    // We are reading the output of the child process as a stream of bytes, 10 at a time in this case
    $text = fread($out, 10);
    // In order to stop when data is finished we need to keep track of read bytes
    $total += strlen($text);
    print $text;
    // We stop when the total read bytes equals what we expected
} while ($total < $sentBytesCount);

// Closing the standard output handle is not really needed, but it is appropriate
$command->closeStdoutHandle();


// Wait for child to finish
print PHP_EOL;
print 'WAITING CHILD...';
$command->close();
print PHP_EOL;
print 'CHILD FINISHED';
print PHP_EOL;
