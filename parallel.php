<?php

use Symfony\Component\Process\Process;

require __DIR__ . '/vendor/autoload.php';

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

if (preg_match('/^[0-9]+(?:\.[0-9]+)?$/', $rate) !== 1) {
    printf('%s is not a valid value for rate.' . PHP_EOL, $rate);
}

if (preg_match('/^[0-9]+$/', $concurrency) !== 1) {
    printf('%s is not a valid value for concurrency.' . PHP_EOL, $concurrency);
    exit(1);
}

$rate        = (float) $rate;
$concurrency = (int) $concurrency;

if ($rate === 0.0) {
    echo 'rate cannot be zero.', PHP_EOL;
    exit(1);
}

if ($concurrency === 0) {
    echo 'concurrency cannot be zero.', PHP_EOL;
    exit(1);
}

$command = $argv;

$sleepTime = (int) (1000000.0 / $rate);

$processes = [];

$filter = static function(Process $process) {
    return $process->isRunning();
};

for (;;) {
    $time = microtime(true);
    $processes = array_values(array_filter($processes, $filter));

    if (count($processes) < $concurrency) {
        $process = new Process($command);
        $process->start(static function($type, $data) {
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
