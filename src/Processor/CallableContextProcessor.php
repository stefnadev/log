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
	 * @param array{context:array<string, mixed>} $record
	 * @return array{context:array<string, mixed>}
	 */
	public function __invoke(array $record)
	{
		foreach ($record['context'] as $key => &$value) {
			if (\is_callable($value)) {
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
