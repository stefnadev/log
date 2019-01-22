<?php declare(strict_types=1);

namespace Stefna\Logger\Filters;

use Psr\Log\LogLevel;
use Stefna\Logger\LogLevelTranslator;

class ActivateLevelFilter implements FilterInterface
{
	/** @var string */
	private $activateLevel;
	private $active = false;

	public function __construct(string $activateLevel = LogLevel::ERROR)
	{
		$this->activateLevel = 7 - LogLevelTranslator::getLevelNo($activateLevel);
	}

	public function __invoke(string $level, string $message, array $context): bool
	{
		if (!$this->active) {
			$levelNr = 7 - LogLevelTranslator::getLevelNo($level);
			$this->active = $this->activateLevel <= $levelNr;
		}
		return $this->active;
	}
}
