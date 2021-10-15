<?php declare(strict_types=1);

namespace Stefna\Logger\Processor;

use Stefna\Logger\Handler\DatadogHandler;

final class DatadogProcessor
{
	/**
	 * @param array{context:array<string, mixed>, channel:?string, ddtags: string|array|null} $record
	 * @return array{context:array<string, mixed>, channel:?string, ddtags: ?string}
	 */
	public function __invoke(array $record): array
	{
		$tags = [];
		if (isset($record['context'][DatadogHandler::TAGS]) && !is_array($record['context'][DatadogHandler::TAGS])) {
			foreach ($record['context'][DatadogHandler::TAGS] as $key => $value) {
				$tags[] = (is_string($key) ? $key . ':' : '') . $value;
			}
			// remove from context to avoid duplicate data
			unset($record['context'][DatadogHandler::TAGS]);
		}

		if (isset($record['channel']) || isset($record['context']['channel'])) {
			$tags[] = 'channel:' . ($record['channel'] ?: $record['context']['channel'] ?: 'unknown');
		}

		// needs to be at top level for datadog to handle them
		$record[DatadogHandler::TAGS] = implode(',', $tags);

		if (isset($record['context'][DatadogHandler::SERVICE]) && !isset($record[DatadogHandler::SERVICE])) {
			$record[DatadogHandler::SERVICE] = $record['context'][DatadogHandler::SERVICE];
			unset($record['context'][DatadogHandler::SERVICE]);
		}

		if (isset($record['context'][DatadogHandler::HOSTNAME]) && !isset($record[DatadogHandler::HOSTNAME])) {
			$record[DatadogHandler::HOSTNAME] = $record['context'][DatadogHandler::HOSTNAME];
			unset($record['context'][DatadogHandler::HOSTNAME]);
		}

		return $record;
	}
}
