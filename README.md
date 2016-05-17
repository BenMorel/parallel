# Parallel

A command-line script to run commands in parallel at a fixed rate.

## What is it?

Ever needed to execute a command `n` times per second, allowing these commands to run simultaneously if the first command
has not finished executing when the second is started? This tool does just that.
 
As a safeguard, you must specify the maximum number of processes allowed to run simultaneously. If the commands take
too long to execute, and the maximum number of processes is reached, the next execution(s) will be skipped until
a slot becomes available.

## How to use it?

Ensure that you have PHP installed, and download [parallel.phar](https://github.com/BenMorel/parallel/blob/master/bin/parallel.phar?raw=true).
Alternatively, you can install it with Composer.

Then run:

    ./parallel.phar Rate Concurrency Command [...]

Where:

- `Rate` is the number of executions per second
- `Concurrency` is the maximum number of concurrent processes
- `Command` is the command to execute, with its optional arguments

Example:

    ./parallel.phar 10 20 date "+%X %N"

This would output 10 lines per second, such as:

    04:27:12 PM 031273903
    04:27:12 PM 121497348
    04:27:12 PM 221800224
    04:27:12 PM 322584008
    04:27:12 PM 423034796
    ...

To stop, just press <kbd>Ctrl</kbd><kbd>C</kbd>.
