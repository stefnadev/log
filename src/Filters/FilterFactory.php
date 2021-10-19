<?php declare(strict_types=1);

namespace Stefna\Logger\Filters;

class FilterFactory
{
	/** @var array<string, array{0: class-string, 1: string[]}> */
	private static $map = [
		CallbackFilter::KEY => [CallbackFilter::class, ['callback']],
		ExcludeLogLevelFilter::KEY => [ExcludeLogLevelFilter::class, ['level']],
		MaxLogLevelFilter::KEY => [MaxLogLevelFilter::class, ['level']],
		MinLogLevelFilter::KEY => [MinLogLevelFilter::class, ['level']],
		LogLevelRangeFilter::KEY => [LogLevelRangeFilter::class, ['min', 'max']],
		TimeLimitFilter::KEY => [TimeLimitFilter::class, ['cache', 'interval']],
	];

	/**
	 * @param array{0: string, 1: array<string, mixed>} $filterConfig
	 */
	public function createFilter(array $filterConfig): ?FilterInterface
	{
		[$filterName, $filterArgs] = $filterConfig;
		if (!isset(self::$map[$filterName])) {
			return null;
		}

		[$cls, $clsArgs] = self::$map[$filterName];
		foreach ($clsArgs as $arg) {
			if (!isset($filterArgs[$arg])) {
				throw new \InvalidArgumentException("Missing argument: '$arg'");
			}
		}

		return new $cls(...array_values($filterArgs));
	}
}
