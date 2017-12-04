# Parallel

A command-line script to run commands in parallel at a fixed rate.

## What is it?

Ever needed to execute a command `n` times per second, allowing these commands to run simultaneously if the first command
has not finished executing when the second is started? This tool does just that.
 
As a safeguard, you must specify the maximum number of processes allowed to run simultaneously. If the commands take
too long to execute, and the maximum number of processes is reached, the next execution(s) will be skipped until
a slot becomes available.

## When is it useful?

Let's say you have a script that queries a remote API. Your request limit on this API is 5 requests per second.

You can't just keep 5 instances of the script running in parallel:

- if the API response time is lower than 1s, you would need to pause each script to ensure that it lasts at least 1s
- if the API response time is greater than 1s, you would not fully use your request allowance

Parallel.phar can start 5 instances of your script every second, regardless of how many are already running, *up to a limit specified by you*. This limit is necessary to avoid server & network congestion in case of sudden network issue, slowdown or lock.

## How to use it?

Ensure that you have PHP installed, and download [parallel.phar](https://github.com/BenMorel/parallel/blob/0.1.0/bin/parallel.phar?raw=true).
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

In this example, there cannot be more than 20 processes running in parallel; this is irrelevant for our `date` example as the execution is really fast, but would be important for long-running scripts, potentially invoking a remote API.

To stop, just press <kbd>Ctrl</kbd> + <kbd>C</kbd>.
