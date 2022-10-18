<?php declare(strict_types=1);

namespace Stefna\Logger\Processor;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

class ChannelProcessor implements ProcessorInterface
{
	public function __invoke(LogRecord $record): LogRecord
	{
		$context = $record->context;
		if (!isset($context['channel'])) {
			return $record;
		}

		$channel = (string)$context['channel'];
		unset($context['channel']);

		return $record->with(
			context: $context,
			channel: $channel,
		);
	}
}
