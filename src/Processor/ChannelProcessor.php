<?php declare(strict_types=1);

namespace Stefna\Logger\Processor;

class ChannelProcessor
{
	/**
	 * @param array{context:array<string, mixed>} $record
	 * @return array{context:array<string, mixed>, channel?: string}
	 */
	public function __invoke($record)
	{
		if (isset($record['context']['channel'])) {
			$record['channel'] = (string)$record['context']['channel'];
			unset($record['context']['channel']);
		}
		return $record;
	}
}
