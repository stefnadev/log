<?php declare(strict_types=1);

namespace Stefna\Logger\Processor;

class ChannelProcessor
{
	public function __invoke($record)
	{
		if (isset($record['context']['channel'])) {
			$record['channel'] = $record['context']['channel'];
			unset($record['context']['channel']);
		}
		return $record;
	}
}
