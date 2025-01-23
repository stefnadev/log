<?php declare(strict_types=1);

namespace Stefna\Logger\Formatter;

use Monolog\Formatter\JsonFormatter;
use Monolog\LogRecord;

final class CloudWatchFormatter extends JsonFormatter
{
	private const DATE_FORMAT = 'Y-m-d\TH:i:s.vp';

	public function format(LogRecord $record): string
	{
		$context = $record->context;
		$requestId = 'empty';
		if (isset($context['requestId'])) {
			$requestId = $context['requestId'];
			unset($context['requestId']);
		}
		$line = parent::format($record->with(context: $context));

		return sprintf(
			"%s\t%s\t%s\t%s",
			$record->datetime->format(self::DATE_FORMAT),
			$requestId,
			$record->level->getName(),
			$line,
		);
	}
}
