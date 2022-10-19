<?php declare(strict_types=1);

namespace Stefna\Logger\Processor;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;
use Stefna\Logger\Handler\DatadogHandler;

final class DatadogProcessor implements ProcessorInterface
{
	public function __invoke(LogRecord $record): LogRecord
	{
		$context = $record->context;
		$tags = [];
		if (isset($context[DatadogHandler::TAGS]) && !is_array($context[DatadogHandler::TAGS])) {
			foreach ($context[DatadogHandler::TAGS] as $key => $value) {
				$tags[] = (is_string($key) ? $key . ':' : '') . $value;
			}
			// remove from context to avoid duplicate data
			unset($context[DatadogHandler::TAGS]);
		}

		if ($record->channel) {
			$tags[] = 'channel:' . $record->channel;
		}

		// todo needs to be moved from processor to handler since we can't modify the record
		/*
		// needs to be at top level for datadog to handle them
		$record[DatadogHandler::TAGS] = implode(',', $tags);

		if (isset($context[DatadogHandler::SERVICE]) && !isset($record[DatadogHandler::SERVICE])) {
			$record[DatadogHandler::SERVICE] = $context[DatadogHandler::SERVICE];
			unset($context[DatadogHandler::SERVICE]);
		}

		if (isset($context[DatadogHandler::HOSTNAME]) && !isset($record[DatadogHandler::HOSTNAME])) {
			$record[DatadogHandler::HOSTNAME] = $context[DatadogHandler::HOSTNAME];
			unset($context[DatadogHandler::HOSTNAME]);
		}
		*/
		return $record;
	}
}
