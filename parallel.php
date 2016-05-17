<?php

use Symfony\Component\Process\Process;

require 'vendor/autoload.php';

$script = array_shift($argv);

if ($argc < 4) {
    printf('Usage: %s rate concurrency command' . PHP_EOL, $script);
    echo '  rate         The number of executions per second', PHP_EOL;
    echo '  concurrency  The maximum number of concurrent processes', PHP_EOL;
    echo '  command      The command to execute', PHP_EOL;
    exit(1);
}

$rate = array_shift($argv);
$concurrency = array_shift($argv);

$command = implode(' ', $argv);

$sleepTime = (int) (1000000 / $rate);

$processes = [];

$filter = function(Process $process) {
    return $process->isRunning();
};

for (;;) {
    $time = microtime(true);
    $processes = array_values(array_filter($processes, $filter));

    if (count($processes) < $concurrency) {
        $process = new Process($command);
        $process->start(function($type, $data) {
            if ($type === Process::OUT) {
                fwrite(STDOUT, $data);
            } elseif ($type === Process::ERR) {
                fwrite(STDERR, $data);
            } else {
                echo 'Unknown output type ', $type, PHP_EOL;
                exit(1);
            }
        });

        $processes[] = $process;
    }

    $microsecondsSpent = (int) (1000000 * (microtime(true) - $time));
    $microsecondsSleep = $sleepTime - $microsecondsSpent;

    if ($microsecondsSleep > 0) {
        usleep($microsecondsSleep);
    }
}
