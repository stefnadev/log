<?php declare(strict_types=1);

namespace Stefna\Logger\Filters;

use Psr\Log\LogLevel;
use Stefna\Logger\LogLevelTranslator;

class LogLevelRangeFilter implements FilterInterface
{
	/** @var int */
	private $minLevel;
	/** @var int */
	private $maxLevel;

	public function __construct(
		string $minLevel = LogLevel::DEBUG,
		string $maxLevel = LogLevel::EMERGENCY
	) {
		$this->minLevel = 7 - LogLevelTranslator::getLevelNo($minLevel);
		$this->maxLevel = 7 - LogLevelTranslator::getLevelNo($maxLevel);
	}

	/**
	 * @inheritdoc
	 */
	public function __invoke(string $psrLevel, string $message, array $context = []): bool
	{
		$level = 7 - LogLevelTranslator::getLevelNo($psrLevel);
		return $this->minLevel <= $level && $this->maxLevel >= $level;
	}

	public function getMinLevel(): int
	{
		return $this->minLevel;
	}

	public function getMaxLevel(): int
	{
		return $this->maxLevel;
	}
}
