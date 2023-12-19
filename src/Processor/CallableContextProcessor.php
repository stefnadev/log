<?php declare(strict_types=1);

namespace Stefna\Logger\Processor;

/**
 * Processor that will execute all callable argument in the context array that
 * way we can deffer executing some heavy calculation that we only want done
 * if the message will actually get logged
 */
class CallableContextProcessor
{
	/**
	 * Can be used in context to allow a callback to be executed if the log entry is used
	 */
	public const CALLBACK = '_callback-processor';

	/**
	 * @param array{context:array<string, mixed>} $record
	 * @return array{context:array<string, mixed>}
	 */
	public function __invoke(array $record)
	{
		if (isset($record['context'][self::CALLBACK])) {
			$callback = $record['context'][self::CALLBACK];
			$callback($record);
			unset($record['context'][self::CALLBACK]);
		}

		foreach ($record['context'] as $key => &$value) {
			if (!is_string($value) && \is_callable($value)) {
				try {
					$value = $value();
				}
				catch (\Throwable $t) {
					//ignore errors
				}
			}
		}

		return $record;
	}
}
