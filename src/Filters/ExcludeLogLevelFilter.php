<?php declare(strict_types=1);

namespace Stefna\Logger\Filters;

class ExcludeLogLevelFilter implements FilterInterface
{
	public const KEY = 'exclude';

	/** @var string[] */
	private array $excludedLogLevel;

	public function __construct(string ...$excludedLogLevel)
	{
		$this->excludedLogLevel = $excludedLogLevel;
	}

	/**
	 * @inheritdoc
	 */
	public function __invoke(string $level, string|\Stringable $message, array $context = []): bool
	{
		return !\in_array($level, $this->excludedLogLevel, true);
	}
}
