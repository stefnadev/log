<?php declare(strict_types=1);

namespace Stefna\Logger\Formatter;

use Monolog\Formatter\JsonFormatter;

final class CloudWatchFormatter extends JsonFormatter
{
	public function format(array $record)
	{
		$line = parent::format($record);
		if (isset($record['context']['requestId'])) {
			$line = $record['context']['requestId'] . "\t" . $record['level'] . "\t" . $line;
		}

		return $line;
	}
}