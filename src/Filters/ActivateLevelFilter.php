<?php declare(strict_types=1);

namespace Stefna\Logger\Filters;

use Monolog\Level;
use Psr\Log\LogLevel;

class ActivateLevelFilter implements FilterInterface
{
	private Level $activateLevel;
	private bool $active = false;

	/**
	 * @phpstan-param LogLevel::*|Level $activateLevel
	 */
	public function __construct(string|Level $activateLevel = Level::Error)
	{
		$this->activateLevel = $activateLevel instanceof Level ? $activateLevel : Level::fromName($activateLevel);
	}

	/**
	 * @inheritdoc
	 * @phpstan-param LogLevel::* $level
	 */
	public function __invoke(string $level, string|\Stringable $message, array $context): bool
	{
		if (!$this->active) {
			$this->active = $this->activateLevel->includes(Level::fromName($level));
		}
		return $this->active;
	}
}
