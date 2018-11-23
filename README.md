# Stefan Logger

## Stefna\Logger\Manager

Class that handles creation of logger classes.

This is where you configure your main logger.

### Methods

* `pushProcessor(callable $callback, string $channel = null): Manager`
    If channel is set the process will only be applied to that channel.
    If no instance of channel exists the process will be silently ignored

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

$monolog = new \Monolog\Logger('main-channel', $handlers, $proccess);
$manager = new \Stefna\Logger\Manager($monolog, new \Stefna\Logger\Filters\FilterFactory());

\Stefna\Logger\Logger::setManager($manager);

$filters = [
	['min-level', \Psr\Log\LogLevel::ALERT],
	[
		'callback',
		function(string $level, string $message, array $context) {
			return isset($context['exception']);
		},
	],
	['time-limit', [$simpleCache, new DateInterval('P1D')]]
];

\Stefna\Logger\Logger::setChannelConfig(
    'test-channel',
    new Stefna\Logger\Config\Config('test-channel', $filters[[, $proccess], $handlers])
);

$logger = \Stefna\Logger\Logger::getLogger('test-channel');

```
