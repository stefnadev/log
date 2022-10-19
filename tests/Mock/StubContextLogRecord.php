<?php declare(strict_types=1);

namespace Stefna\Logger\Mock;

use Monolog\Level;
use Monolog\LogRecord;

final class StubContextLogRecord extends LogRecord
{
	public function __construct(array $context = [])
	{
		parent::__construct(
			new \DateTimeImmutable(),
			'test',
			Level::Debug,
			'',
			$context,
		);
	}
}
