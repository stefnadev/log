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
	public function __invoke($psrLevel, $message, array $context = []): bool
	{
		$level = 7 - LogLevelTranslator::getLevelNo($psrLevel);
		return $this->minLevel <= $level && $this->maxLevel >= $level;
	}
}
