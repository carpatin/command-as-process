<?php

define('BUFFER_SIZE', 2);
file_put_contents('log.txt', '');

$action = getenv('PROCESS_WITH');

// This script just uppercases anything that comes to STDIN and writes to STDOUT
while (!feof(STDIN))
{
    $text = fread(STDIN, BUFFER_SIZE);
    file_put_contents('log.txt', "### child received bytes '$text'".PHP_EOL, FILE_APPEND);

    switch ($action)
    {
        case 'strtoupper':
            fwrite(STDOUT, strtoupper($text));
            break;
        default:
            fwrite(STDOUT, $text);
    }
    file_put_contents('log.txt', "### child wrote processed bytes for '$text'".PHP_EOL, FILE_APPEND);
}

file_put_contents('log.txt', "### child sleeps 5 seconds".PHP_EOL, FILE_APPEND);
sleep(5);

file_put_contents('log.txt', '### child exiting', FILE_APPEND);
