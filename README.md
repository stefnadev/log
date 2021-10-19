# Stefna Logger

## Stefna\Logger\ManagerInterface

Class that handles creation of logger classes.

This is where you configure your main logger.

### Methods

* `pushProcessor(callable $callback, string $channel = null): Manager`
    If channel is set the process will only be applied to that channel.
    If no instance of channel exists the process will be silently ignored

## Stefna\Logger\MonologManager

Manager that uses Monolog as the main logger.

It has one extra method on the manager and that's `pushHandler` that is so that you can configure custom handlers
for select channels

* `pushHandler(HandlerInterface $handler, $channel = null): Manager` 
    If channel is set the handler will only be applied to that channel.
    If no instance of channel exists the process will be silently ignored

## Stefna\Logger\Logger Methods

### setManager

Set log manager if this is not done all calls to `getLogger` will return a `NullLogger`

### getManager

Get manager to add process or handler to main logger

### setChannelConfig

Add a channel specific config

### setGlobalConfig

Add multiple config options with one call

### getLogger

Retrives a logging instance for `channel`

This method will create the logger if it don't exists and it check for configs for the specified `channel`


## Example

```php
<?php declare(strict_types=1);

use Stefna\Logger\Filters\MinLogLevelFilter;
use Stefna\Logger\Filters\CallbackFilter;
use Stefna\Logger\Filters\TimeLimitFilter;

$monolog = new \Monolog\Logger('main-channel', $handlers, $proccess);
$manager = new \Stefna\Logger\MonologManager($monolog, new \Stefna\Logger\Filters\FilterFactory());

\Stefna\Logger\Logger::setManager($manager);

$filters = [
	[MinLogLevelFilter::KEY, ['level' => \Psr\Log\LogLevel::ALERT]],
	[
		CallbackFilter::KEY,
		[
			'callback' => function(string $level, string $message, array $context) {
				return isset($context['exception']);
			},
		],
	],
	[TimeLimitFilter::KEY, ['cache' => $simpleCache, 'interval' => new DateInterval('P1D')]]
];

\Stefna\Logger\Logger::setChannelConfig(
    'test-channel',
    new Stefna\Logger\Config\Config('test-channel', $filters[[, $proccess], $handlers])
);

$logger = \Stefna\Logger\Logger::getLogger('test-channel');

```

## Setup of a crash logger

```php
<?php declare(strict_types=1);

$logger = new SimpleFileLogger('path/to/save/crash.log');
//or
$logger = new SystemErrorLogger();

$crashLogger = new BufferFilterLogger(
    $logger,
    new ActivateLevelFilter(LogLevel::ERROR)
);

// Will not add to log file
$crashLogger->debug('test');

// Will add all message prior and after this to the log
// This is so that we get a complete story of what happened during the execution
$crashLogger->error('error');

```

## Setup debouncer filter

This filter is meant to be used to prevent logs from being filed with the same errors

### Setup

```php
<?php declare(strict_types=1);

use Stefna\Logger\Filters\DebounceFilter;
use Stefna\Logger\Logger\FilterLogger

$debounceFilter = new DebounceFilter(function($level, $message, $context) use ($cache) {
	// create cache key
	$key = md5(serialize([$message, $context]));
	if ($cache->has($key)) {
		return false;
	}
	$debounceInterval = $context[DebounceFilter::DEBOUNCE_INTERVAL];
	$cache->set($key, true, new DateInterval($debounceInterval));
	return true;
});

$logger = new FilterLogger($mainLogger, $debounceFilter);
$logger->alert('Db connect error', [
	DebounceFilter::DEBOUNCE_INTERVAL => 'PT1H', // only log once an hour
]);
```

