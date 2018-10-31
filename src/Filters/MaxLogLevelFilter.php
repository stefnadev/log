<?php declare(strict_types=1);

namespace Stefna\Logger\Filters;

use Psr\Log\LogLevel;

class MaxLogLevelFilter extends LogLevelRangeFilter
{
	public function __construct(string $maxLevel = LogLevel::EMERGENCY)
	{
		parent::__construct(LogLevel::DEBUG, $maxLevel);
	}
}
