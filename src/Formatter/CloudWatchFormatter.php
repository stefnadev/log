<?php declare(strict_types=1);

namespace Stefna\Logger\Formatter;

use Monolog\Formatter\JsonFormatter;

final class CloudWatchFormatter extends JsonFormatter
{
	public function format(array $record)
	{
		$dateTime = $record['datetime'];
		if (!$dateTime instanceof \DateTimeInterface) {
			// this should be impossible
			$dateTime = new \DateTimeImmutable();
		}
		$record['datetime'] = $dateTime->format('Y-m-d\TH:i:s.up');
		$line = parent::format($record);
		if (isset($record['context']['requestId'])) {
			$line = sprintf(
				"%s\t%s\t%s\t%s",
				$dateTime->format('Y-m-d\TH:i:s.vp'),
				$record['context']['requestId'],
				$record['level_name'],
				$line
			);
		}

		return $line;
	}
}
