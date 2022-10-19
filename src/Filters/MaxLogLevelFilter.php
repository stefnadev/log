<?php declare(strict_types=1);

namespace Stefna\Logger\Filters;

use Monolog\Level;

class MaxLogLevelFilter extends LogLevelRangeFilter
{
	public const KEY = 'max-level';

	public function __construct(string|Level $maxLevel = Level::Emergency)
	{
		parent::__construct(Level::Debug, $maxLevel);
	}
}
