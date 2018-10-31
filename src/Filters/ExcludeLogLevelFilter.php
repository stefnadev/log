<?php declare(strict_types=1);

namespace Stefna\Logger\Filters;

class ExcludeLogLevelFilter implements FilterInterface
{
	/** @var string */
	private $excludedLogLevel;

	public function __construct(string $excludedLogLevel)
	{
		$this->excludedLogLevel = $excludedLogLevel;
	}

	/**
	 * @inheritdoc
	 */
	public function __invoke($level, $message, array $context = []): bool
	{
		return $this->excludedLogLevel !== $level;
	}
}