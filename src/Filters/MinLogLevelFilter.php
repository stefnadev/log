<?php declare(strict_types=1);

namespace Stefna\Logger\Filters;

use Monolog\Level;

class MinLogLevelFilter extends LogLevelRangeFilter
{
	public const KEY = 'min-level';

	public function __construct(string|Level $minLevel = Level::Emergency)
	{
		parent::__construct($minLevel);
	}
}
