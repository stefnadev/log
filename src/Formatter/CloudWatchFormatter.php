<?php declare(strict_types=1);

namespace Stefna\Logger\Formatter;

use Monolog\Formatter\JsonFormatter;

final class CloudWatchFormatter extends JsonFormatter
{
	public function format(array $record)
	{
		if (isset($record["datetime"]) && ($record["datetime"] instanceof \DateTimeInterface)) {
			$dateTime = $record['datetime'];
			$record['datetime'] = $record['datetime']->format('c');
		}
		if (!isset($dateTime)) {
			$dateTime = new \DateTimeImmutable();
		}
		$line = parent::format($record);
		if (isset($record['context']['requestId'])) {
			$line = sprintf(
				"%s\t%s\t%s\t%s",
				$dateTime->format('Y-m-d\TH:i:s.v\Z'),
				$record['context']['requestId'],
				$record['level_name'],
				$line
			);
		}

		return $line;
	}
}
