<?php declare(strict_types=1);

namespace Stefna\Logger\Processor;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

/**
 * Processor that will execute all callable argument in the context array that
 * way we can deffer executing some heavy calculation that we only want done
 * if the message will actually get logged
 */
class CallableContextProcessor implements ProcessorInterface
{
	/**
	 * Can be used in context to allow a callback to be executed if the log entry is used
	 */
	public const CALLBACK = '_callback-processor';

	public function __invoke(LogRecord $record): LogRecord
	{
		$context = $record->context;
		if (isset($context[self::CALLBACK])) {
			$callback = $context[self::CALLBACK];
			$callback($record);
			unset($context[self::CALLBACK]);
		}

		foreach ($context as $key => &$value) {
			if (!is_string($value) && \is_callable($value)) {
				try {
					$value = $value();
				}
				catch (\Throwable $t) {
					//ignore errors
				}
			}
		}

		return $record->with(context: $context);
	}
}
