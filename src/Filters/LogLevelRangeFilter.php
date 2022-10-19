<?php declare(strict_types=1);

namespace Stefna\Logger\Filters;

use Monolog\Level;
use Psr\Log\LogLevel;

class LogLevelRangeFilter implements FilterInterface
{
	public const KEY = 'log-level';

	/**
	 * @phpstan-param LogLevel::*|Level $minLevel
	 * @phpstan-param LogLevel::*|Level $maxLevel
	 */
	public function __construct(
		private readonly string|Level $minLevel = Level::Debug,
		private readonly string|Level $maxLevel = Level::Emergency,
	) {}

	/**
	 * @inheritdoc
	 * @phpstan-param LogLevel::* $psrLevel
	 */
	public function __invoke(string $psrLevel, string|\Stringable $message, array $context = []): bool
	{
		$level = Level::fromName($psrLevel);
		return $this->getMinLevel()->includes($level) && $level->includes($this->getMaxLevel());
	}

	public function getMinLevel(): Level
	{
		return $this->minLevel instanceof Level ? $this->minLevel : Level::fromName($this->minLevel);
	}

	public function getMaxLevel(): Level
	{
		return $this->maxLevel instanceof Level ? $this->maxLevel : Level::fromName($this->maxLevel);
	}
}
