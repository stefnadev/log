<?php declare(strict_types=1);

namespace Stefna\Logger\Filters;

use Psr\Log\LogLevel;

class MinLogLevelFilter extends LogLevelRangeFilter
{
	public const KEY = 'min-level';

	public function __construct(string $minLevel = LogLevel::EMERGENCY)
	{
		parent::__construct($minLevel);
	}
}
